<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CategoryRepository $categoryRepository, ArticleRepository $articleRepository)
    {
        $categories = $categoryRepository->findAllCategoryOrderedByNewest();
        $articles = $articleRepository->findHome();
        return $this->render('index/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/artall", name="artall")
     */
    public function artall(CategoryRepository $categoryRepository, ArticleRepository $articleRepository)
    {
        $categories = $categoryRepository->findAllCategoryOrderedByNewest();
        $articles = $articleRepository->findArticlePublishedOrderedByNewest();
        return $this->render('index/artall.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/parcat/{id}", name="parcat")
    */
    public function affichage(Category $category, CategoryRepository $categoryRepository, ArticleRepository $articleRepository)
    {
        $categories=$categoryRepository->findAllCategoryOrderedByNewest();
        $articles=$articleRepository->findArticlePublishedOrderedByNewestCategory($category);

        return $this->render('index/parcat.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'title' => $category,
        ]);
    }

    /**
     * @Route("/artishow/{id}", name="artishow")
    */
    public function artishow(Article $article, Request $request, CommentRepository $commentRepository): Response
    {
        $em=$this->getDoctrine()->getManager();
        $comment = new Comment();
        $form=$this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($comment !=null){           
            if($form->isSubmitted() && $form->isValid()) {
            
                $comment->setAuthor($this->getUser());
                $comment->setArticle($article);
                $comment->setDatePublished(new \DateTime()); 
            
                $em->persist($comment);
                $em->flush();

            return $this->redirect($request->getUri());

            }
        }

        return $this->render('index/artishow.html.twig', [
            'article' => $article,
            'article' => $article,
            'comment' => $commentRepository->findBy(['article' => $article]),
            'form_comment'=> $form->createView(),
        ]);
    }
}

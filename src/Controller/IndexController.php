<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
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
    public function index(CategoryRepository $categoryRepository)
    {

        $em = $this->getDoctrine()->getManager();
        $categories = $categoryRepository->findAllPublishedOrderedByNewest();
        $articles = $em->getRepository(Article::class)->findAll();
        return $this->render('index/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/parcat/{id}", name="cat")
    */
    public function affichage(Category $category, CategoryRepository $categoryRepository)
    {
        $title = $category;
        $em = $this->getDoctrine()->getManager();
        $categories=$categoryRepository->findAllPublishedOrderedByNewest();
        $articles=$em->getRepository(Article::class)->findBy(['category' => $category]);

        $title = $category;

        return $this->render('index/parcat.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'title' => $title,
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

<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository, CommentRepository $commentRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $article = $articleRepository->findAll(),
            'comment' => $commentRepository->findBy(['article' => $article]),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if( $this->getUser() == $article->getAuthor() | $this->getUser()->hasRole('ROLE_ADMIN')){

            if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($article);
                $entityManager->flush();

                if($this->getUser() == $article->getAuthor()){
                    return $this->redirectToRoute('article_own');
                }
                else{
                    return $this->redirectToRoute('article_index');
                }                
            }

        }

        else{
            $this->addFlash("danger", "don't have rights on it");
            return $this->redirectToRoute('index');
        }        
    }

    /**
     * @Route("/{id}/adminpublished", name="published")
     */
    public function published(Request $request, Article $article): Response
    {
        if( $this->getUser() == $article->getAuthor() | $this->getUser()->hasRole('ROLE_ADMIN')){

            if ($article->getState() == false) {
                
                $article->setState(true);
                $article->setDatePublished(new \DateTime());
                               
            }
            else{
                $article->setState(false);
                $article->setDatePublished(null);
            }

            $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($article);
                $entityManager->flush();

            return $this->redirectToRoute('article_index');


        }

        else{
            $this->addFlash("danger", "don't have rights on it");
            return $this->redirectToRoute('index');
        }        
    }
}

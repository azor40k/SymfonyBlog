<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/author")
*/
class ArticleOwnController extends AbstractController
{
    /**
     * @Route("/own", name="article_own")
     */
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $article->setDateCreated(new \DateTime());
            $article->setAuthor($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_own');
        }

        return $this->render('article_own/index.html.twig', [
            'articles' => $articleRepository->findBy(['author' => $this->getUser()]),
            'form_new_own' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="artown_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="artown_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $idAuthor = $article->getAuthor();
        $currentUser = $this->getUser();
        if( $currentUser != $idAuthor ){

            $this->addFlash("danger", "Not Yours can't edit");
            return $this->redirectToRoute('article_own');
        }
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_own');
        }

        return $this->render('article_own/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/{id}/published", name="article_published")
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

            if($this->getUser() == $article->getAuthor() & $this->getUser()->hasRole('ROLE_ADMIN')){
                    return $this->redirectToRoute('article_own');
                }
            elseif($this->getUser() == $article->getAuthor()){
                    return $this->redirectToRoute('article_own');
                }
            else{
                    return $this->redirectToRoute('article_index');
                } 

        }

        else{
            $this->addFlash("danger", "don't have rights on it");
            return $this->redirectToRoute('index');
        }        
    }
}

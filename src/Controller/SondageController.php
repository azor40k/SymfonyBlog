<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 /**
* @Route("/sondage")
*/
class SondageController extends AbstractController
{
    /**
     * @Route("/", name="sondage")
     */
    public function index(QuestionRepository $questionRepository)
    {

        return $this->render('sondage/index.html.twig', [
            'questions' => $questionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/questshow/{id}", name="questshow")
     */
    public function questshow(QuestionRepository $questionRepository, Question $question, AnswerRepository $answerRepository) : Response
    {
        return $this->render('sondage/questshow.html.twig', [
            'question' => $questionRepository->findOneBy(['id' => $question]),
            'answer' => $answerRepository->findBy(['question' => $question])
        ]);
    }

    /**
     * @Route("/scoreadd/{id}", name="scoreadd")
     */
    public function scoreadd(Answer $answer)
    {
        
      $answer->setScore($answer->getScore() +1);

      $em = $this->getDoctrine()->getManager();
      $em->persist($answer);
      $em->flush();

      return $this->redirectToRoute('questshow', ['id'=> $answer->getQuestion()->getId()]);
      

    }
}

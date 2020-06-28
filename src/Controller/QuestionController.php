<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response,JsonResponse};
use App\Entity\{Question,QuestionChoice};
use App\Form\{QuestionType,QuestionChoiceType};
use App\Repository\{QuestionRepository,QuestionChoiceRepository};


class QuestionController extends AbstractController
{
    /**
     * @Route("/question", name="question")
     */
    public function index()
    {
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
        ]);
    }

    /**
     * @Route("/new-choice/{question}", name="question_choice_new", methods={"GET","POST"})
     */
    public function addChoice(Request $request,Question $question): Response
    {
        $questionChoice = new QuestionChoice();
        $questionForm = $this->createForm(QuestionChoiceType::class, $questionChoice);
        $questionForm->handleRequest($request);

        if ($request->isMethod('POST')) {

            $questionChoice->setChoice($request->request->get('choice'));
            $questionChoice->setQuestion($question);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($questionChoice);
            $entityManager->flush();

            $responseHtml = $this->renderView('question_choice/_show_choice.html.twig', [
                'choice' => $questionChoice,
            ]);

            $response['status'] = 'success';
            $response['message'] = 'Choice has been added';
            $response['htmlResponse'] = $responseHtml;

            return new JsonResponse($response);
        }


        return $this->render('question_choice/_new_choice.html.twig', [
            'status' => 'success',
            'form' => $questionForm->createView(),
            'questionId' => $question->getId()
        ]);
    }

    /**
     * @Route("/delete-question/{question}", name="question_delete")
     */
    public function deleteQuestion(Request $request,Question $question): Response
    {   
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($question);
        $entityManager->flush();

        $response['status'] = 'success';
        $response['message'] = 'Question has been deleted';

        return new JsonResponse($response);
    }

    /**
     * @Route("/delete-choice/{choice}", name="question_choice_delete")
     */
    public function deleteQuestionChoice(Request $request,QuestionChoice $choice): Response
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($choice);
        $entityManager->flush();

        $response['status'] = 'success';
        $response['message'] = 'Choice has been deleted';

        return new JsonResponse($response);
    }
}

<?php

namespace App\Controller;

use App\Entity\{DynamicForm,Question};
use App\Entity\Response as QuestionResponse;
use App\Form\{DynamicFormType,QuestionType};
use App\Repository\{DynamicFormRepository,QuestionRepository,QuestionChoiceRepository,ResponseRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response,JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form")
 */
class DynamicFormController extends AbstractController
{
    /**
     * @Route("/", name="dynamic_form_index", methods={"GET"})
     */
    public function index(Request $request, DynamicFormRepository $dynamicFormRepository): Response
    {
        return $this->render('dynamic_form/index.html.twig', [
            'dynamic_forms' => $dynamicFormRepository->findAll(),
        ]);
    }

    /**
     * @Route("/user/", name="dynamic_form_index_user", methods={"GET"})
     */
    public function indexUser(Request $request, DynamicFormRepository $dynamicFormRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'dynamic_forms' => $dynamicFormRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="dynamic_form_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $dynamicForm = new DynamicForm();
        $form = $this->createForm(DynamicFormType::class, $dynamicForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dynamicForm);
            $entityManager->flush();

            // return $this->redirectToRoute('dynamic_form_index');
            return $this->redirectToRoute('dynamic_form_show',['id' => $dynamicForm->getId()]);
        }

        return $this->render('dynamic_form/new.html.twig', [
            'dynamic_form' => $dynamicForm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="dynamic_form_show", methods={"GET"})
     */
    public function show(DynamicForm $dynamicForm, QuestionRepository $questionRepository): Response
    {
        $questions = $questionRepository->findByForm($dynamicForm);
        
        return $this->render('dynamic_form/show.html.twig', [
            'questions' => $questions,
            'dynamic_form' => $dynamicForm,
        ]);
    }

    /**
     * @Route("/{id}/responses", name="dynamic_form_show_responses", methods={"GET"})
     */
    public function showResponses(DynamicForm $dynamicForm, ResponseRepository $responseRepository): Response
    {
        $responses = $responseRepository->findBy(
            ['form' => $dynamicForm]
        );
        
        return $this->render('dynamic_form/show_responses.html.twig', [
            'responses' => $responses,
            'dynamic_form' => $dynamicForm,
        ]);
    }

    /**
     * @Route("/user/{id}", name="dynamic_form_user_show", methods={"GET","POST"})
     */
    public function showUser(DynamicForm $dynamicForm, ResponseRepository $responseRepository, QuestionRepository $questionRepository, QuestionChoiceRepository $questionChoiceRepository, Request $request): Response
    {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $questions = $questionRepository->findByForm($dynamicForm);

        $responses = $responseRepository->findBy(
            ['user' => $user, 'form' => $dynamicForm]
        );
        
        if($responses)
        {
            return $this->render('user/show_response.html.twig', [
                'responses' => $responses,
                'dynamic_form' => $dynamicForm,
            ]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        if($request->isMethod('POST'))
        {
            foreach($questions as $question)
            {   
                $questionResponse = $request->get($question->getId());

                $response = new QuestionResponse();
                $response->setQuestion($question);
                $response->setUser($user);
                $response->setForm($dynamicForm);

                $answerString = '';

                foreach($questionResponse as $postResponse)
                {
                    if($question->getType() == 'SINGLE-LINE')
                    {
                        $answerString = $postResponse;
                    } else {
                        $choice = $questionChoiceRepository->find($postResponse);
                        $answerString = $answerString ? $answerString.' ,'.$choice->getChoice() : $answerString.$choice->getChoice();
                    }    
                }

                $response->setAnswer($answerString);
                $entityManager->persist($response);
                $entityManager->flush();
            }
            return $this->redirectToRoute('dynamic_form_index_user');

        }

        return $this->render('user/show_user.html.twig', [
            'questions' => $questions,
            'dynamic_form' => $dynamicForm,
        ]);
    }

    /**
     * @Route("/show/new-question/{form}", name="dynamic_form_question", methods={"GET","POST"})
     */
    public function addQuestion(Request $request,DynamicForm $form): Response
    {
        $question = new Question();
        $questionForm = $this->createForm(QuestionType::class, $question);
        $questionForm->handleRequest($request);

        if ($request->isMethod('POST')) {

            $question->setQuestion($request->request->get('question'));
            $question->setType($request->request->get('type'));
            $question->setForm($form);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            $responseHtml = $this->renderView('question/_show_question.html.twig', [
                'question' => $question,
            ]);

            $response['status'] = 'success';
            $response['message'] = 'Question has been added';
            $response['htmlResponse'] = $responseHtml;

            return new JsonResponse($response);
        }


        return $this->render('question/_new_question.html.twig', [
            'form' => $questionForm->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="dynamic_form_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DynamicForm $dynamicForm): Response
    {
        $form = $this->createForm(DynamicFormType::class, $dynamicForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dynamic_form_index');
        }

        return $this->render('dynamic_form/edit.html.twig', [
            'dynamic_form' => $dynamicForm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="dynamic_form_delete", methods={"DELETE"})
     */
    // public function delete(Request $request, DynamicForm $dynamicForm): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$dynamicForm->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($dynamicForm);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('dynamic_form_index');
    // }

}

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
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CustomFileManager;

/**
 * @Route("/form")
 */
class DynamicFormController extends AbstractController
{
    protected $entityManager;
    protected $customFileManager;
    protected $dynamicFormRepository;
    protected $questionRepository;
    protected $questionChoiceRepository;
    protected $responseRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CustomFileManager $customFileManager,
        DynamicFormRepository $dynamicFormRepository,
        QuestionRepository $questionRepository,
        QuestionChoiceRepository $questionChoiceRepository,
        ResponseRepository $responseRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->customFileManager = $customFileManager;
        $this->dynamicFormRepository = $dynamicFormRepository;
        $this->questionRepository = $questionRepository;
        $this->questionChoiceRepository = $questionChoiceRepository;
        $this->responseRepository = $responseRepository;
    }


    /**
     * @Route("/admin/", name="dynamic_form_index", methods={"GET"})
     */
    public function index(Request $request, DynamicFormRepository $dynamicFormRepository): Response
    {
        return $this->render('dynamic_form/index.html.twig', [
            'dynamic_forms' => $this->dynamicFormRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/toggle/{id}", name="dynamic_form_toggle", methods={"GET"})
     */
    public function toggle(Request $request, DynamicForm $dynamicForm): Response
    {

        $dynamicForm->setIsActive(!$dynamicForm->getIsActive());
        $this->entityManager->persist($dynamicForm);
        $this->entityManager->flush();

        if($dynamicForm->getIsActive())
            $this->addFlash('message', 'Form is live to customers');
        else    
            $this->addFlash('message', 'Form is hidden from customers');

        return $this->redirectToRoute('dynamic_form_index');
    }

    /**
     * @Route("/admin/new", name="dynamic_form_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $dynamicForm = new DynamicForm();
        $form = $this->createForm(DynamicFormType::class, $dynamicForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $files = $request->files->get('dynamic_form')['file'];
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dynamicForm);
            $entityManager->flush();

            $response = $this->customFileManager->saveImages($files,$dynamicForm); //image saving service

            $this->addFlash('message', 'Form creation successful');
            // return $this->redirectToRoute('dynamic_form_index');
            return $this->redirectToRoute('dynamic_form_show',['id' => $dynamicForm->getId()]);
        }

        return $this->render('dynamic_form/new.html.twig', [
            'dynamic_form' => $dynamicForm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}/edit", name="dynamic_form_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DynamicForm $dynamicForm): Response
    {
        $form = $this->createForm(DynamicFormType::class, $dynamicForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $files = $request->files->get('dynamic_form')['file'];

            $this->entityManager->flush();

            $response = $this->customFileManager->saveImages($files,$dynamicForm); //image saving service
            
            $this->addFlash('message', 'Form edit successful');

            return $this->redirectToRoute('dynamic_form_index');
        }

        return $this->render('dynamic_form/edit.html.twig', [
            'dynamic_form' => $dynamicForm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}", name="dynamic_form_show", methods={"GET"})
     */
    public function show(DynamicForm $dynamicForm): Response
    {
        $questions = $this->questionRepository->findByForm($dynamicForm);
        
        return $this->render('dynamic_form/show.html.twig', [
            'questions' => $questions,
            'dynamic_form' => $dynamicForm,
        ]);
    }

    /**
     * @Route("/admin/{id}/responses", name="dynamic_form_show_responses", methods={"GET"})
     */
    public function showResponses(DynamicForm $dynamicForm): Response
    {
        $responses = $this->responseRepository->findResponses($dynamicForm);
        
        return $this->render('dynamic_form/show_responses.html.twig', [
            'responses' => $responses,
            'dynamic_form' => $dynamicForm,
        ]);
    }

    

    /**
     * @Route("/admin/show/new-question/{form}", name="dynamic_form_question", methods={"GET","POST"})
     */
    public function addQuestion(Request $request,DynamicForm $form): Response
    {
        $question = new Question();
        $questionForm = $this->createForm(QuestionType::class, $question);
        $questionForm->handleRequest($request);

        if ($request->isMethod('POST')) {
            
            if($request->request->get('type') == 'DATETIME-PICKER'){
                $result = $this->questionRepository->findBy(
                    ['type' => 'DATETIME-PICKER', 'form' => $form]
                );
                if($result){
                    $response['status'] = 'exists';
                    $response['message'] = 'Only one datetime picker can be used for a form';

                    return new JsonResponse($response);
                }

            }
            $question->setQuestion($request->request->get('question'));

            $question->setType($request->request->get('type'));
            $question->setForm($form);

            $this->entityManager->persist($question);
            $this->entityManager->flush();

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

    /**
     * @Route("/user/", name="dynamic_form_index_user", methods={"GET"})
     */
    public function indexUser(Request $request): Response
    {
        return $this->render('user/index.html.twig', [
            'dynamic_forms' => $this->dynamicFormRepository->findBy(['isActive' => 1]),
        ]);
    }

    /**
     * @Route("/user/{id}", name="dynamic_form_user_show", methods={"GET","POST"})
     */
    public function showUser(DynamicForm $dynamicForm, Request $request): Response
    {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $questions = $this->questionRepository->findByForm($dynamicForm);

        $responses = $this->responseRepository->findBy(
            ['user' => $user, 'form' => $dynamicForm]
        );
        
        if($responses)
        {
            return $this->render('user/show_response.html.twig', [
                'responses' => $responses,
                'dynamic_form' => $dynamicForm,
            ]);
        }
        
        if($request->isMethod('POST'))
        {
            foreach($questions as $question)
            {   
                $questionResponse = $request->get($question->getId());

                if($questionResponse)
                {
                    $response = new QuestionResponse();
                    $response->setQuestion($question);
                    $response->setUser($user);
                    $response->setForm($dynamicForm);

                    $answerString = '';

                    foreach($questionResponse as $postResponse)
                    {
                        if($question->getType() == 'SINGLE-LINE' || $question->getType() == 'DATETIME-PICKER' )
                        {
                            $answerString = $postResponse;
                        } else {
                            $choice = $this->questionChoiceRepository->find($postResponse);
                            $answerString = $answerString ? $answerString.' ,'.$choice->getChoice() : $answerString.$choice->getChoice();
                        }    
                    }

                    $response->setAnswer($answerString);
                    $this->entityManager->persist($response);
                    $this->entityManager->flush();
                }
                
            }

            $this->addFlash('message', 'Your response has been saved. You can check your response by revisiting the form.');
            return $this->redirectToRoute('dynamic_form_index_user');

        }

        return $this->render('user/show_user.html.twig', [
            'questions' => $questions,
            'dynamic_form' => $dynamicForm,
        ]);
    }

}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use App\Service\MailManager;
use App\Entity\User;
use App\Repository\UserRepository;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/landing-page", name="app_login_success")
     */
    public function loginSuccess(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        return $this->render('security/landingPage.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

      /**
     * @Route("/createuserjohndoe", name="app_test")
     */
    public function test(UserRepository $userRepository)
    {   
        $entityManager = $this->getDoctrine()->getManager();
            
        $user = $userRepository->findOneBy(['email' => 'johndoe@company.com']);

        if($user){
            $entityManager->remove($user);
            $entityManager->flush();
        } else {

        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('johndoe@company.com');
        // $user->setPassword('password');
        }
        $password = $user->generatePassword('password'); //function modified to stop random password generation for demo app
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        $encoder = $encoderFactory->getEncoder($user);

        $user->encodePassword($encoder);
        $user->setRawPassword($password);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('John Doe created');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function registerAction(Request $request, MailManager $mailManager)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
                // encode the plain password
                $password = $user->generatePassword($user->getPassword()); //function modified to stop random password generation for demo app
                $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
                $encoders = [
                    User::class => $defaultEncoder,
                ];
                $encoderFactory = new EncoderFactory($encoders);
                $encoder = $encoderFactory->getEncoder($user);

                $user->encodePassword($encoder);
                $user->setRawPassword($password);
                $em->persist($user);
                $em->flush();
                $mailManager->registrationMail($user);
                $this->get('session')->getFlashBag()->set(
                    'flashSuccess',
                    $user->getFirstname() . ' account successfully created'
                );
                return $this->redirectToRoute('app_login');
        }

        return $this->render('user/user_registration.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
    public function test()
    {   
        $entityManager = $this->getDoctrine()->getManager();
            
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('johndoe@company.com');
        $user->setPassword('password');

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('John Doe created');
    }
}

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
use Symfony\Component\Debug\Exception\FlattenException;

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
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/createuserjohndoe", name="app_create_user")
     */
    public function createUser(UserRepository $userRepository)
    {   
        $entityManager = $this->getDoctrine()->getManager();
            
        $user = $userRepository->findOneBy(['email' => 'johndoe@company.com']);

        if($user){
            $entityManager->remove($user);
            $entityManager->flush();
        } 
        // else {

        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('johndoe@company.com');
        $user->setRoles(['ROLE_USER']);
        // }
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
     * @Route("/createadmin", name="app_create_admin")
     */
    public function createAdmin(UserRepository $userRepository)
    {   
        $entityManager = $this->getDoctrine()->getManager();
            
        $user = $userRepository->findOneBy(['email' => 'admin@company.com']);

        if($user){
            $entityManager->remove($user);
            $entityManager->flush();
        } else {

            $user = new User();
            $user->setFirstName('Admin');
            $user->setLastName('User');
            $user->setEmail('admin@company.com');
            $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
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

        return new Response('Admin user created');
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

    /**
     * @Route("/error-message", name="app_error_message")
     */
    public function errorAction(Request $request, FlattenException $exception): Response
    {
        $errorCode = $exception->getStatusCode();

        if($errorCode == 403)
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig',[
            ]);

        if($errorCode == 404)
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig',[
            ]);

        return $this->render('bundles/TwigBundle/Exception/error.html.twig',[
        ]);
    }

    /**
     * @Route("/login-redirect", name="app_login_redirect")
     */
    public function loginRedirect(Request $request): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN',$user->getRoles()))
            return $this->redirectToRoute('dynamic_form_index');

        return $this->redirectToRoute('dynamic_form_index_user');
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;

class MailManager
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var EngineInterface */
    private $templating;

    /**
     * MailManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $templating
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        \Swift_Mailer $mailer,
        EngineInterface $templating
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param User $user
     * @return null
     */
    public function registrationMail(User $user)
    {
        $emailTemplate = $this->templating->render(
            'emails/registration.html.twig',
            [
                'password' => $user->getRawPassword(),
                'firstName' => $user->getFirstname()
            ]
        );

        $message = (new \Swift_Message('Form Builder Registration'))
            ->setFrom('akarifmohammed@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($emailTemplate,'text/html')
        ;

        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @return null
     */
    // public function forgotPassword(User $user)
    // {
    //     $emailTemplate = $this->templating->render(
    //         'emails/forgot_password.html.twig',
    //         [
    //             'password' => $user->getRawPassword(),
    //             'username' => $user->getUsername()
    //         ]
    //     );

    //     $message = (new \Swift_Message('SpadeDESK Registration'))
    //         ->setFrom('s.rao@spadecorporation.com')
    //         ->setTo($user->getEmail())
    //         ->setBody($emailTemplate,'text/html')
    //     ;

    //     $this->mailer->send($message);
    // }

}
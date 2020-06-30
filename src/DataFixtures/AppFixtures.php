<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $entityManager = $this->getDoctrine()->getManager();
        $adminUser = new User();
        $adminUser->setFirstName('Admin');
        $adminUser->setLastName('Role');
        $adminUser->setEmail('admin@company.com');
        $adminUser->setRoles(['ROLE_ADMIN']);

        $password = $adminUser->generatePassword('password'); //function modified to stop random password generation for demo app
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        $encoder = $encoderFactory->getEncoder($adminUser);

        $adminUser->encodePassword($encoder);
        $adminUser->setRawPassword($password);

        $manager->persist($adminUser);

        $user1 = new User();
        $user1->setFirstName('John');
        $user1->setLastName('Doe');
        $user1->setEmail('johndoe@company.com');
        $user1->setRoles(['ROLE_USER']);

        $password = $user1->generatePassword('password'); //function modified to stop random password generation for demo app
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        $encoder = $encoderFactory->getEncoder($user1);

        $user1->encodePassword($encoder);
        $user1->setRawPassword($password);

        $manager->persist($user1);
        
        $user2 = new User();
        $user2->setFirstName('User2');
        $user2->setLastName('company');
        $user2->setEmail('user2@company.com');
        $user2->setRoles(['ROLE_USER']);

        $password = $user2->generatePassword('password'); //function modified to stop random password generation for demo app
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        $encoder = $encoderFactory->getEncoder($user2);

        $user2->encodePassword($encoder);
        $user2->setRawPassword($password);

        $manager->persist($user2);

        $user3 = new User();
        $user3->setFirstName('User3');
        $user3->setLastName('company');
        $user3->setEmail('user3@company.com');
        $user3->setRoles(['ROLE_USER']);

        $password = $user3->generatePassword('password'); //function modified to stop random password generation for demo app
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        $encoder = $encoderFactory->getEncoder($user3);

        $user3->encodePassword($encoder);
        $user3->setRawPassword($password);

        $manager->persist($user3);

        $manager->flush();
    }
}

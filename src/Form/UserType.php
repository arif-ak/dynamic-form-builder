<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\{TextType,PasswordType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        // ->add('userName',
        //     TextType::class,
        //     [
        //         'label_attr' => [],
        //         'attr' => ['class' => 'form-control'],
        //     ])
        ->add('firstName',
            TextType::class,
            [
                'label_attr' => [],
                'attr' => ['class' => 'form-control'],
            ])
        ->add('lastName',
            TextType::class,
            [
                'label_attr' => [],
                'attr' => ['class' => 'form-control'],
            ])
            
        ->add('email',
            EmailType::class,
            [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
        ->add('password',
            PasswordType::class,
            [
                'label_attr' => [],
                'attr' => ['class' => 'form-control'],
            ]
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true
        ]);
    }
}

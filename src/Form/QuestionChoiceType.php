<?php

namespace App\Form;

use App\Entity\QuestionChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextType,ButtonType};

class QuestionChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('choice',
            TextType::class,
            [
                'attr' => [
                    'class' => 'choice-row-choice input-sm',
                    'placeholder' => 'Enter choice text here....'
                ],
            ]
        )
        ->add('save', ButtonType::class, [
            'label' => 'Save',
            'attr' => ['class' => 'btn-success btn-sm save-choice'],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionChoice::class,
        ]);
    }
}

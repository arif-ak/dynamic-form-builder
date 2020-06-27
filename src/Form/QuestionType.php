<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextType,ChoiceType,ButtonType};

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'question-row-question',
                        'placeholder' => 'Enter question text here....'
                    ],
                ]
            )
            ->add('type',
                ChoiceType::class,
                [
                    'choices' => Question::TYPE_ARRAY,
                    'placeholder' => 'Select type',
                    'attr' => [
                        'class' => 'question-row-type',
                    ],
                ]
            )
            ->add('save', ButtonType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn-success save-question'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}

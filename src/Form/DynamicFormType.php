<?php

namespace App\Form;

use App\Entity\DynamicForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextType,ButtonType};

class DynamicFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',
                TextType::class,
                [
                    'label' => 'Service name',
                ]
            )
            ->add('description',
                TextType::class,
                [
                    'label' => 'Service description',
                ]
            )
            ->add('regularPrice')
            ->add('salesPrice')
            ->add('uniqueId')
            ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DynamicForm::class,
        ]);
    }
}

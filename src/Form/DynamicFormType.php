<?php

namespace App\Form;

use App\Entity\DynamicForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextType,ButtonType,FileType};

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
            ->add('file',FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'label' => 'Upload files',
                'required'   => false
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DynamicForm::class,
        ]);
    }
}

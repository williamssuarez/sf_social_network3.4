<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nombre',
                'required' => 'required',
                'empty_data' => '',
                'attr' => array(
                    'class' => 'form-name form-control name-input'
                )
            ))
            ->add('surname', TextType::class, array(
                'label' => 'Apellido',
                'required' => 'required',
                'attr' => array(
                    'class' => 'form-name form-control surname-input'
                )
            ))
            ->add('nick' , TextType::class, array(
                'label' => 'Nick',
                'required' => 'required',
                'attr' => array(
                    'class' => 'form-nick form-control nick-input'
                )
            ))
            ->add('email' , EmailType::class, array(
                'label' => 'Correo Electronico',
                'required' => 'required',
                'attr' => array(
                    'class' => 'form-email form-control email-input'
                )
            ))
            ->add('bio' , TextareaType::class, array(
                'label' => 'Biografia',
                'required' => false,
                'attr' => array(
                    'class' => 'form-bio form-control'
                )
            ))
            ->add('image' , FileType::class, array(
                'label' => 'Foto',
                'required' => false,
                'data_class' => null,
                'attr' => array(
                    'class' => 'form-image form-control'
                )
            ))
            ->add('Guardar', SubmitType::class, array(
                "attr" => array(
                    "class" => "form-submit btn btn-success register-input"
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\User',
        ]);
    }
}

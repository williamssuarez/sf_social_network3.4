<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PrivateMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['empty_data'];
        $builder
            ->add('receiver', EntityType::class, array(
                'class' => 'App\Entity\User',
                'query_builder' => function($er) use($user){
                    return $er->getFollowingUsers($user);
                },
                'choice_label' => function ($user){
                    return $user->getName()." ".$user->getSurname()." - @".$user->getNick();
                },
                'label' => 'Para: ',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('message' , TextareaType::class, array(
                'label' => 'Mensaje',
                'required' => 'required',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('image' , FileType::class, array(
                'label' => 'Imagen',
                'required' => false,
                'data_class' => null,
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('file' , FileType::class, array(
                'label' => 'Archivo',
                'required' => false,
                'data_class' => null,
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('Enviar', SubmitType::class, array(
                "attr" => array(
                    "class" => "btn btn-success register-input"
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\PrivateMessage'
        ]);
    }
}

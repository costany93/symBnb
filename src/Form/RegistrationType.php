<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, $this->getConfiguration('Nom', 'Entrer votre nom'))
            ->add('lastname',TextType::class, $this->getConfiguration('Prénom', 'Entrer votre prénom'))
            ->add('email',EmailType::class, $this->getConfiguration('Email', 'Entrer adresse mail'))
            ->add('picture',UrlType::class, $this->getConfiguration('Picture', 'Entrer l\'url de la photo'))
            ->add('hash',PasswordType::class, $this->getConfiguration('Mot de passe', 'entrer un bon mot de passe'))
            ->add('passwordConfirm',PasswordType::class, $this->getConfiguration('Confirmer le Mot de passe', 'confirmer le mot de passe'))
            ->add('intro',TextType::class, $this->getConfiguration('Introduction', 'Bref présentation de vous'))
            ->add('description',TextareaType::class, $this->getConfiguration('Description', 'Présentation vous en détails'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

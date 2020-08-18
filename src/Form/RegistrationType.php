<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, array('label' => false))
            ->add('prenom', null, array('label' => false))
            ->add('date_naissance', null, array('label' => false))
            ->add('site_rattachement', null, array('label' => false))
            ->add('enseigne', null, array('label' => false))
            ->add('email', EmailType::class, array('label' => false))
            ->add('password', PasswordType::class, array('label' => false))
            ->add('confirm_password', PasswordType::class, array('label' => false))
            ->add('portable', null, array('label' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

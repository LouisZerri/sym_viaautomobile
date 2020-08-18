<?php

namespace App\Form;

use App\Entity\Challenge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('periode')
            ->add('description')
            ->add('imageFile', FileType::class, [
                'required' => true,
                'label' => 'Ajouter une image',
                'multiple' => false
            ])
            ->add('en_cours', NumberType::class, [
                'label' => 'Le challenge est-il encore en cours ? (1 - Oui | 0 - Non)'
            ])
            ->add('vainqueur', null, ['required' => false])
            ->add('imageFileAccueil', FileType::class, [
                'required' => false,
                'label' => 'Ajouter une image pour la page d\'accueil',
                'multiple' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Vente;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_vente', null, array('label' => 'Date de la vente', 'required' => true))
            ->add('immatriculation', null, array('label' => 'Immatriculation du véhicule', 'required' => true))
            ->add('livree', CheckboxType::class, array('label' => 'Livrée','required' => false))
            ->add('frais_mer', CheckboxType::class, array('label' => 'Frais de mise à la route', 'required' => false))
            ->add('garantie', CheckboxType::class, array('label' => 'Garantie', 'required' => false))
            ->add('financement', CheckboxType::class, array('label' => 'Financement', 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null,[
                "label" => "Nom :",
                "required" => false,

            ])
            ->add('rue', null, [
                'label' => 'Rue :',
                'required' => false,
                "attr" => [
                    "pattern" => false
                ],
            ])

            ->add('latitude',null,[
                "label" => "Latitude :",
                "required" => false,
                "attr" => [
                    "pattern" => false
                ],
                'invalid_message' => 'La latitude doit contenir uniquement des chiffres et des points'
            ])
            ->add('longitude',null,[
                "label" => "Longitude :",
                "required" => false,
                "attr" => [
                    "pattern" => false
                ],
                'invalid_message' => 'La longitude doit contenir uniquement des chiffres et des points'
            ])
            ->add('ville',EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}

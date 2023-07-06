<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CreateActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null,[
                "label" => "Nom de la sortie :",
                "required" => false,
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'by_reference' => true,
            ])

            ->add('duree',null,[
                "label" => "Durée :",
            ])
            ->add('dateCloture', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'by_reference' => true,
            ])
            ->add('nbInscriptionsMax',null,[
                "label" => "Nombre de place :",
                "required" => false
            ])
            ->add('descriptionInfos',TextareaType::class,[
                "label" => "Description et infos :"
            ])
            ->add('site',EntityType::class,[
                "class" => Site::class,
                "choice_label" => "nom",
                "label" => "Campus :",
                "multiple" => false
            ])
            #->add('etat')
            ->add('ville',EntityType::class,[
                'class' => Ville::class,
                'choice_label' => "nom",
                'label' => 'Ville :',
                'mapped' => false,
                'placeholder' => 'Choisir une ville',
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])

            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'multiple' => false
            ])
            ->add('rue',TextType::class,[
                'label' => 'Rue :',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ->add('codePostal',TextType::class,[
                'label' => 'Code Postal :',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ->add('latitude',TextType::class,[
                'label' => 'Latitude :',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ->add('longitude',TextType::class,[
                'label' => 'Longitude :',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

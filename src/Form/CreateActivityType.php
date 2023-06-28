<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                "label" => "Nom de la sortie :"
            ])
            ->add('dateDebut',null,[
                "label" => "Date et heure de la sortie :"
            ])
            ->add('duree',null,[
                "label" => "Durée :"
            ])
            ->add('dateCloture')
            ->add('nbInscriptionsMax',null,[
                "label" => "Nombre de place :"
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
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => "nom",
                'label' => 'Lieu :',
                'multiple' => false
            ])
            ->add('ville',EntityType::class,[
                'class' => Ville::class,
                'choice_label' => "nom",
                'label' => 'Ville :',
                'mapped' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ->add('rue',TextType::class,[
                'label' => 'Rue :',
                'mapped' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ->add('codePostal',TextType::class,[
                'label' => 'Code Postal :',
                'mapped' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ->add('latitude',TextType::class,[
                'label' => 'Latitude :',
                'mapped' => false,
                'attr' => [
                    'class' => 'mt-3 text-center',
                ],
            ])
            ->add('longitude',TextType::class,[
                'label' => 'Longitude :',
                'mapped' => false,
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

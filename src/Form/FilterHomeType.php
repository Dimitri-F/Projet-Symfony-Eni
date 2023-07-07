<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Site;

class FilterHomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder' => '',
                'required' => false
            ])
            ->add('nomSortie', TextType::class, [
                'required' => false
            ])
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isInscrit', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isPassed', CheckboxType::class, [
                'required' => false,
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('compte', EntityType::class, [
                'class' => User::class,
                'label' => 'Pseudo',
                'choice_label' => 'Pseudo',
                'mapped' => false
            ])
            ->add('prenom', null, [
                'label' => 'Prénom'
            ])
            ->add('nom', null, [
                'label' => 'nom'
            ])
            ->add('telephone', null, [
                'label' => 'téléphone'
            ])
            ->add('email', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'mapped' => false,
            ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
            ])
            ->add('motDePasse2', PasswordType::class, [
                'label' => 'Confirmation',
                'mapped' => false,
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

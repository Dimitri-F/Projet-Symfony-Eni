<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('compte', TextType::class, [
                'label' => 'Pseudo',
                'mapped' => false,
                'data' => null
            ])
            ->add('prenom', null, [
                'label' => 'Prénom',
                'data' => null
            ])
            ->add('nom', null, [
                'label' => 'nom',
                'data' => null
            ])
            ->add('telephone', null, [
                'label' => 'téléphone',
                'data' => null
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
                'mapped' => false,
                'data' => null
            ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'data' => null
            ])
            ->add('motDePasse2', PasswordType::class, [
                'label' => 'Confirmation',
                'mapped' => false,
                'data' => null
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'mapped' => false
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

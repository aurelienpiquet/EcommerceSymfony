<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifyPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => "Mon adresse email"
            ])
            ->add('old_password', PasswordType::class, [
                'label' => 'Votre ancien mot de passe',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Ecriver votre ancien mot de passe ici...'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                    'type'=> PasswordType::class,
                    'mapped' => false,
                    'invalid_message' => 'Les deux champs doivent être identiques',
                    'required' => true,
                    'first_options' => ['label' => 'Votre nouveau mot de passe', 'attr' => ['placeholder' => 'Ecrivez votre nouveau mot de passe ici...']],
                    'second_options' => ['label' => 'Confirmer le nouveau mot de passe', 'attr' => ['placeholder' => 'Confirmez votre nouveau mot de passe ici...']],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => "Modifier votre mot de passe"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
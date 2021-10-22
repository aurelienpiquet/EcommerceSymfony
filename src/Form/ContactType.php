<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => "Votre nom *",
                'constraints' => new Length(null, 2, 40),
                'required' => true,
            ])
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom *",
                'constraints' => new Length(null, 2, 40),
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre email *",
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => "Votre message *",
                'required' => true,
                'attr' => [
                    'placeholder' => "En quoi pouvons-nous vous aider?"
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer votre message",
                'attr' => [
                    'class' => 'btn btn-success btn-lg'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom de la nouvelle adresse (livraison, facturation...) *",
            ])
            ->add('firstname', TextType::class, [
                'label' => "Prénom *",
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom *",
            ])
            ->add('compagny', TextType::class, [
                'label' => "Nom de l'entreprise",
                'required' => false,
            ])
            ->add('street', TextType::class, [
                'label' => "Adresse *",
            ])
            ->add('zipcode', TextType::class, [
                'label' => "Code postal *",
            ])
            ->add('city', TextType::class, [
                'label' => "Ville *",
            ])
            ->add('country', CountryType::class, [
                'preferred_choices' => ['FR'],
                'label' => "Pays *",
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Ajouter cette adresse",
                'attr' => [
                    'class' => 'btn btn-success btn-lg'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}

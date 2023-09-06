<?php

namespace App\Form;

use App\Entity\Advertisement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'max_length' => 100,
                'min_length' => 10,
                'attr' => [
                    'minlength' => 10,
                    'maxlength' => 100,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'annonce',
                'max_length' => 1000,
                'min_length' => 20,
                'attr' => [
                    'minlength' => 20,
                    'maxlength' => 1000,
                ],
            ])
            ->add('price', NumberType::class, [
                'min_value' => 0,
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'max_length' => 100,
                'min_length' => 2,
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 100,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advertisement::class,
        ]);
    }
}

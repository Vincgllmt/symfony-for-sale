<?php

namespace App\Form;

use App\Entity\Advertisement;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;

class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 100,
                    ]),
                ],
                'attr' => [
                    'minlength' => 10,
                    'maxlength' => 100,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'annonce',
                'constraints' => [
                    new Length([
                        'min' => 20,
                        'max' => 1000,
                    ]),
                ],
                'attr' => [
                    'minlength' => 20,
                    'maxlength' => 1000,
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix de l\'annonce',
                'constraints' => [
                  new GreaterThan(0),
                ],
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 100,
                    ]),
                ],
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 100,
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
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

<?php

namespace App\Form;

use App\Entity\User;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre adresse email',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre adresse email doit contenir au moins {{ limit }} caractères',
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères',
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new PasswordStrength([
                            'minLength' => 10,
                            'tooShortMessage' => 'Le mot de passe doit contenir au moins {{length}} caractères',
                            'minStrength' => 4,
                            'message' => 'Le mot de passe doit contenir des lettres majuscules et minuscules, des chiffres et des caractères spéciaux',
                        ]
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

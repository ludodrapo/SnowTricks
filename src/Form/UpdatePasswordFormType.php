<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Mot de passe actuel",
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez saisir votre mot de pass actuel.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => "Votre mot de passe doit être composé d'au moins {{ limit }} caractères.",
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                        'maxMessage' => "Votre mot de passe dépasse les 255 caractères, ça va être difficile à mémoriser, non ?!"
                    ])
                ]
            ])
            ->add('newPassword1', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Nouveau mot de passe",
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez saisir un nouveau mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => "Votre mot de passe doit être composé d'au moins {{ limit }} caractères.",
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                        'maxMessage' => "Votre mot de passe dépasse les 255 caractères, ça va être difficile à mémoriser, non ?!"
                    ])
                ]
            ])
            ->add('newPassword2', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Encore une fois (pour être sûr)",
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez saisir une deuxième fois votre nouveau mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => "Votre mot de passe doit être composé d'au moins {{ limit }} caractères.",
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                        'maxMessage' => "Votre mot de passe dépasse les 255 caractères, ça va être difficile à mémoriser, non ?!"
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

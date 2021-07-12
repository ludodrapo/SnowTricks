<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;


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
                    new SecurityAssert\UserPassword([
                        'message' => "Le mot de passe saisi n'est pas celui associé à ce profil."
                    ])
                ]
            ])
            ->add('newPassword1', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Nouveau mot de passe",
                'mapped' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '#^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s)([\W\w]{8,16})$#',
                        'message' => "Votre mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial."
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => "Votre mot de passe doit être composé d'au moins {{ limit }} caractères.",
                        'max' => 16,
                        'maxMessage' => "Votre mot de passe ne doit pas dépasser les {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('newPassword2', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Encore une fois (pour être sûr)",
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

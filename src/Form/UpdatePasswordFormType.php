<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * class UpdatePasswordFormType
 * @package App\Form
 */
class UpdatePasswordFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => "Mot de passe actuel",
                'mapped' => false,
                'constraints' => [
                    new SecurityAssert\UserPassword([
                        'message' => "Le mot de passe saisi n'est pas celui associé à ce profil."
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '#^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s)([\W\w]{8,16})$#',
                        'message' => "Votre mot de passe doit contenir une majuscule, 
                        une minuscule, un chiffre, un caractère spécial 
                        et être composé d'entre 8 et 16 caractères."
                    ])
                ],
                'mapped' => false,
                'first_options'  => [
                    'attr' => [
                        'class' => 'password-to-check'
                    ],
                    'label' => 'Nouveau mot de passe'
                ],
                'second_options' => ['label' => 'Encore une fois (pour être sûr(e))'],
                'invalid_message' => "Les deux mots de passe saisis ne sont pas identiques."
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

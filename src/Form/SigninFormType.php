<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;


/**
 * class SigninFormType
 * @package App\Form
 */
class SigninFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('screenName', TextType::class, [
                'label' => "Nom d'utilisateur",
                'constraints' => [
                    new NotBlank([
                        'message' => "Vous devez saisir un nom d'utilisateur.",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Votre nom d'utilisateur doit être composé d'au moins {{ limit }} caractères.",
                        'max' => 255
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => "Mot de passe",
                'mapped' => false,
                'attr' => [
                    'class' => 'password-to-check',
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '#^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s)([\W\w]{8,16})$#',
                        'message' => "Votre mot de passe doit contenir une majuscule, une minuscule, un chiffre, un caractère spécial et être composé d'entre 8 et 16 caractères."
                    ])
                ]
            ])
            ->add('idPhoto', FileType::class, [
                'label' => "Votre avatar (jpeg, jpg, gif, png, webp au format carré)",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => "Ce fichier est trop lourd, il faudrait evisager de le compresser ou d'en réduire la taille.",
                        'mimeTypes' => 'image/*',
                        'mimeTypesMessage' => "Attention, ce fichier n'est pas une image.",
                        'allowLandscape' => false,
                        'allowLandscapeMessage' => "Pour votre avatar, il nous faut un format carré (1:1)",
                        'allowPortrait' => false,
                        'allowPortraitMessage' => "Pour votre avatar, il nous faut un format carré (1:1)"
                    ])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les conditions d'utilisation du site.",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => "Vous devez accepter les conditions d'utilisaton du site pour vous inscrire.",
                    ])
                ]
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

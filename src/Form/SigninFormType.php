<?php

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
use Symfony\Component\Validator\Constraints\File;

class SigninFormType extends AbstractType
{
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
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Mot de passe",
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez saisir un mot de passe.',
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
            ->add('idPhoto', FileType::class, [
                'label' => "Votre avatar au format carré (jpeg, jpg, gif, png, webp)",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => "Ce fichier est trop lourd, il faudrait evisager de le compresser ou d'en réduire la taille.",
                        'mimeTypes' => 'image/*',
                        'mimeTypesMessage' => "Attention, ce fichier n'est pas une image."
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

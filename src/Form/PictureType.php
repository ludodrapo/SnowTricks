<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;

/**
 * class PictureType
 * @package App\Form
 */
class PictureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Photo (jpeg/jpg/png/webp) au format paysage',
                    'required' => false,
                    'constraints' => [
                        new Image([
                            'mimeTypes' => 'image/*',
                            'mimeTypesMessage' => "Ce fichier n'est pas une image.",
                            'allowPortrait' => false,
                            'allowPortraitMessage' => "Le format portrait n'est pas adapté.",
                            'allowSquare' => false,
                            'allowSquareMessage' => "Le format carré n'est pas adapté."
                        ])
                    ]
                ]
            )
            ->add(
                'alt',
                TextType::class,
                [
                    'label' => 'Court descriptif de la photo',
                    'required' => true,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => "Votre descriptif doit quand même dépasser les {{ limit }} caractères."
                        ])
                    ]
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Photo (jpeg/jpg/png/webp)',
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'maxSizeMessage' => "Ce fichier est trop lourd car il dépasse les 1024 Ko",
                            'mimeTypes' => 'image/*',
                            'mimeTypesMessage' => "Ce fichier n'est pas une image."
                        ])
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', TextType::class, [
                'label' => 'Url de la vidéo',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez l\'url de la vidéo sélectionnée'
                ],
                'constraints' => new Url([
                    'message' => "{{ value }} n'est pas une url valide."
                ])
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class
        ]);
    }
}

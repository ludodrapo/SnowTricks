<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

/**
 * class VideoType
 * @package App\Form
 */
class VideoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class
        ]);
    }
}

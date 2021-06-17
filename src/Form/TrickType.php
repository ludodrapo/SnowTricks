<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du trick',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez le nom du trick'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description complète',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rédigez ici sa description complète'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control'],
                'placeholder' => '-- Choisissez une catégorie --',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

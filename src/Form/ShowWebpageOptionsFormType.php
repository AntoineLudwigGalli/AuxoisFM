<?php

namespace App\Form;

use App\Entity\ShowWebpageOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShowWebpageOptionsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('backgroundColor', ColorType::class, [
                    'label' => 'Couleur de fond :',
            ])
            ->add('textColor', ColorType::class, [
                'label' => 'Couleur du texte:',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShowWebpageOptions::class,
        ]);
    }
}

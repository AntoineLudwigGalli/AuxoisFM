<?php

namespace App\Form;

use App\Entity\DynamicContent;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class DynamicContentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('content', CKEditorType::class, [

                'label' => false,
                'purify_html' => true,
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères',
                        'max' => 5000,
                        'maxMessage' => 'Le contenu doit contenir au maximum {{ limit }} caractères'
                    ]),
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => "Enregistrer"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DynamicContent::class,
        ]);
    }
}

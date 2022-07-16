<?php

namespace App\Form;

use App\Entity\RadioShow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RadioShowCreationFormType extends AbstractType
{
    private array $allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            todo : ajouter heure et fréquence de l'émission
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'émission',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner le nom de l\'émission'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 300,
                        'minMessage' => "Le nom de l\émission doit contenir au minimum {{ limit }} caractères.",
                        'maxMessage' => "Le nom de l\émission doit contenir au maximum {{ limit }} caractères.",
                    ])
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => "Description de l'émission (facultatif)",
                'attr' => [
                    "rows" => 8,
                ],
                'constraints' => [
                    new Length([
                        'max' => 50000,
                        'maxMessage' => "Le lien doit contenir au maximum {{ limit }} caractères.",
                    ])
                ]
            ])

            ->add('youtubeURL', TextType::class, [
                'label' => "URL de la playlist Youtube (facultatif)",
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => "Le lien doit contenir au maximum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('spotifyURL', TextType::class, [
                'label' => "URL de la playlist Spotify (facultatif)",
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => "Le lien doit contenir au maximum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('deezerURL', TextType::class, [
                'label_html' => true, //Permet de contourner l'échappement des balises HTML dans le label
                'label' => "URL de la playlist Deezer (facultatif) <a href='https://widget.deezer.com/' target='_blank'><sup>?</sup></a>",
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => "Le lien doit contenir au maximum {{ limit }} caractères.",
                    ])
                ]
            ])

            ->add('logo', FileType::class, [
                'label' => 'Sélectionnez le logo de l\'émission (facultatif)',
                'attr' => [
                    'accept' => implode(", ", $this->allowedMimeTypes),
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Fichier trop volumineux ({{ size }} {{ suffix }}). La taille maximum autorisée est de {{ limit }} {{ suffix }}.',
                        'mimeTypes' => $this->allowedMimeTypes,
                        'mimeTypesMessage' => "Ce type de fichier {{ type }} n'est pas autorisé. Les types autorisés sont {{ types }}."
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => "Créer l'émission"
            ])
        ;;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioShow::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}

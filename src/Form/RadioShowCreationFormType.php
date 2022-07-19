<?php

namespace App\Form;

use App\Entity\RadioShow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThan;
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

            ->add('startDate', DateType::class, [
                'label' => 'Sélectionnez la date de la prochaine émission',
                'model_timezone' => 'Europe/Paris', //Date au format FR
                'widget' => 'single_text', //Date au format input date html
                'help' => 'Saisir une date future uniquement',
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir une date valide pour la prochaine émission'
                    ]),
                    new GreaterThan([ //Erreur si date antérieure à aujourd'hui
                        'value' => 'today',
                        'message' => "La date doit être postérieure à aujourd'hui."
                    ]),
                ]
            ])
            ->add('broadcastDay', ChoiceType::class, [
                'label' => "Jour de diffusion",
                'placeholder' => 'Choisissez le jour de diffusion',
                'choices'  => [
                    'Lundi' => "lundi",
                    'Mardi' => "mardi",
                    'Mercredi' => "mercredi",
                    'Jeudi' => "jeudi",
                    'Vendredi' => "vendredi",
                    'Samedi' => "samedi",
                    'Dimanche' => "Dimanche",
                ],
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir un jour de diffusion valide pour votre émission'
                    ]),
                ]
            ])
            ->add('timeInterval', DateIntervalType::class, [
                'label' => "L'émission a lieu tous les ",
                'widget' => 'choice',
                'with_days' => true,
                'with_months' => true,
                'with_years' => true,
                'placeholder' => [
                    'days' => 'Jours',
                    'months' => 'Mois',
                    'years' => 'Ans',
                ],
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir une fréquence valide pour votre émission'
                    ]),
                ]
            ])

            ->add('showTime', TimeType::class, [
                'label' => 'Horaire de diffusion de l\'émission',
                'input' => 'datetime',
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir un horaire valide pour votre émission'
                    ]),
                ]
            ])

            ->add('showDuration', TimeType::class, [
                'label' => 'Durée de l\'émission',
                'input' => 'datetime',
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir une durée valide pour votre émission'
                    ]),
                ]
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

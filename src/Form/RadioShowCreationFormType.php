<?php

namespace App\Form;

use App\Entity\RadioShow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('genre', TextType::class, [
                'label' => 'Genre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner le nom de l\'émission'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => "Le nom de l\émission doit contenir au minimum {{ limit }} caractères.",
                        'maxMessage' => "Le nom de l\émission doit contenir au maximum {{ limit }} caractères.",
                    ])
                ],
            ])
            ->add('logo', FileType::class, [
                'label' => 'Sélectionnez le logo de l\'émission (facultatif)',
                'data_class' => null,
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

            ->add('timeInterval', ChoiceType::class, [
                'label' => "L'émission a lieu ",
                'placeholder' => 'Sélectionnez la fréquence',
                "choices" => [
                    'Certains jours toutes les semaines' => 0,
                    'Tous les jours' => 1,
                    'Tous les 2 jours' => 2,
                    'Tous les 3 jours' => 3,
                    'Tous les 4 jours' => 4,
                    'Tous les 5 jours' => 5,
                    'Tous les 6 jours' => 6,
                    'Toutes les semaines' => 7,
                    'Toutes les 2 semaines' => 14,
                    'Toutes les 3 semaines' => 21,
                    'Toutes les 4 semaines' => 28,
                ],
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir une fréquence valide pour votre émission'
                    ]),
                ]
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
                        'value' => 'yesterday',
                        'message' => "La date doit être postérieure à aujourd'hui."
                    ]),
                ]
            ])
            ->add('broadcastDay', ChoiceType::class, [
                'label' => "Jour(s) de diffusion",
                'expanded' => true,
                'multiple' => true,
                'placeholder' => 'Choisissez le jour de diffusion',
                'choices'  => [
                    'Lundi' => "1",
                    'Mardi' => "2",
                    'Mercredi' => "3",
                    'Jeudi' => "4",
                    'Vendredi' => "5",
                    'Samedi' => "6",
                    'Dimanche' => "7",
                ],
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir un jour de diffusion valide pour votre émission'
                    ]),
                ]
            ])




            ->add('showTime', TimeType::class, [
                'label' => 'Horaire de diffusion:',
                'input' => 'datetime',
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir un horaire valide pour votre émission'
                    ]),
                ]
            ])

            ->add('showDuration', TimeType::class, [
                'label' => 'Durée de l\'émission:',
                'input' => 'datetime',
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank([ // Erreur si le champ n'est pas rempli
                        'message' => 'Merci de saisir une durée valide pour votre émission'
                    ]),
                ]
            ])
            ->add('youtubeURL', TextType::class, [
                'help_html' => true, //Permet de contourner l'échappement des balises HTML dans le label
                'label' => "URL de la playlist Youtube (facultatif)",
                'help' => "Saisir le code identifiant de la playlist présent dans le lien de la playlist (exemple : https://www.youtube.com/playlist?list=<b>PLd-1gAwLCGHPDCa5waBdkUjedPT0CzUtQ</b>)",
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => "Le lien doit contenir au maximum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('spotifyURL', TextType::class, [
                'help_html' => true, //Permet de contourner l'échappement des balises HTML dans le label
                'label' => "URL de la playlist Spotify (facultatif)",
                'help' => "Saisir le code identifiant de la playlist présent dans le lien de la playlist (exemple : https://open.spotify.com/playlist/<b>37i9dQZF1DXe9Gx5fVy1RT</b>)",
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => "Le lien doit contenir au maximum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('deezerURL', TextType::class, [
                'help_html' => true, //Permet de contourner l'échappement des balises HTML dans le label
                'label' => "URL de la playlist Deezer (facultatif)",
                'help' => "Saisir le code identifiant de la playlist présent dans le lien de la playlist (exemple : https://www.deezer.com/fr/playlist/<b>1388965575</b>)",
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => "Le lien doit contenir au maximum {{ limit }} caractères.",
                    ])
                ]
            ])

        ;
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

<?php

namespace App\Form;

use App\Entity\Podcast;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class PodcastFormType extends AbstractType
{
    private array $allowedMimeTypes = [
        'mp3' => 'audio/mpeg',
        'mp4' => 'mp4 audio',
        'ogg' => 'audio/ogg',
        'wav' => 'audio/vnd.wav',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('podcastLink', FileType::class, [
                'label' => 'Sélectionnez un podcast',
                'attr' => [
                    'accept' => implode(", ", $this->allowedMimeTypes),
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez sélectionner un fichier !',
                    ]),
                    new File([
                        'maxSize' => '500M',
                        'maxSizeMessage' => 'Fichier trop volumineux ({{ size }} {{ suffix }}). La taille maximum autorisée est de {{ limit }} {{ suffix }}.',
                        'mimeTypes' => $this->allowedMimeTypes,
                        'mimeTypesMessage' => "Ce type de fichier {{ type }} n'est pas autorisé. Les types autorisés sont {{ types }}."
                    ]),
                ],
            ])
            ->add('broadcastDate')
            ->add('downloadLink')
//            ->add('radioShow')
        ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Podcast::class,
        ]);
    }
}

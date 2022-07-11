<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//             Champs Email
        ->add('email', EmailType::class, [
            'label' => 'Adresse Email',
            'constraints' => [
                new Email([
                    'message' => 'L\'adresse email {{ value }} n\'est pas une adresse valide',
                ]),
                new NotBlank([
                    'message' => 'Merci de saisir une adresse mail.'
                ])
            ]
        ])
            //            Checkbox CGU
            ->add('agreeTerms', CheckboxType::class, [
                'label_html' => true, //Permet de contourner l'échappement des balises HTML dans le label
                //    Mettre le vrai lien CGU
                'label' => 'Accepter les <a href="/legals/cgu" target="_blank">conditions d\'utilisation</a>',
                'mapped' => false, //Permet d'ignorer le contrôle du champ avec les champs dans la BDD pour éviter
                // une erreur symfony
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales d\'utilisation',
                    ]),
                ],
            ])

            //Champs Mot de passe et Confirmation du Mot de passe
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                ],
                'attr' => [
                    'autocomplete' => 'new-password' // Propose l'autocomplétion à l'utilisateur
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/u',
                        'message' => "Votre mot de passe doit contenir obligatoirement au moins une minuscule, une majuscule, un chiffre et un caractère spécial",
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 4096,
                        'minMessage' => "Votre mot de passe doit contenir au moins {{ limit }} caractères",
                        'maxMessage' => 'Votre mot de passe ne peut pas contenir plus de {{ limit }} caractères'
                    ])
                ],
            ])
            // Champ Prénom
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre prénom'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Votre prénom ne peut pas contenir plus de {{ limit }} caractères'
                    ]),
                ],
            ])
            // Champ Nom
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre nom'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Votre nom ne peut pas contenir plus de {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('pseudonym', TextType::class, [
                'label' => 'Pseudo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre pseudo'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Votre pseudo doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Votre pseudo ne peut pas contenir plus de {{ limit }} caractères'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

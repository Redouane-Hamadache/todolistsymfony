<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'firstName'],
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'Nom'],
                'required' => true,
            ])
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'attr' => ['class' => 'Pseudo'],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Choisir une photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            "image/png",
                            "image/jpg",
                            "image/jpeg",
                            "image/gif",
                            "image/webp"
                        ],
                        'mimeTypesMessage' => 'Format Incorrect',
                    ])
                ],
            ])
            // ->add('agreeTerms', CheckboxType::class, [
            //     'mapped' => false,
            //     'constraints' => [
            //         new IsTrue([
            //             'message' => 'You should agree to our terms.',
            //         ]),
            //     ],
            // ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation de mot de passe'],
                'constraints' => [
                    new Regex([
                        'pattern'=> '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                        'match'=> true,
                        'message'=> 'Veuillez entrer un mot de passe contenant 6 caractères dont 1 majusucle, 1 minuscule et 1 chiffre',
                    ]),
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => "S'inscrire",
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

<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', options: [
                'label' => 'Nom d\'utilisateur',
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner une adresse email',
                    ),

                ]
            ])

            ->add('email',EmailType::class, options: [
                'label' => 'adresse mail',
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner un nom d\'utilisateur',
                    ),
                    new Email(message: 'Veuillez renseigner une adresse email valide')
                ]
            ])


            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner un mot de passe',
                    ),
                    new Length(
                        min: 6,
                        max: 4096,
                        minMessage: 'Votre mot de passe doit faire au minimum {{ limit }} caractères',
                    ),
                ],
            ])

            ->add('repeatPlainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Repetez le mot de passe',

                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez confirmer un mot de passe',
                    ),
                    new Length(
                        min: 6,
                        max: 4096,
                    ),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les termes et conditions',
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter les conditions d\'utilisateur',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
            new Callback(function ($data, ExecutionContextInterface $context) {
                $form = $context->getRoot();
                $password = $form->get('plainPassword')->getData();
                $repeat   = $form->get('repeatPlainPassword')->getData();

                if ($password !== $repeat) {
                    $context
                        ->buildViolation('Les mots de passe ne correspondent pas')
                        ->atPath('repeatPlainPassword')
                        ->addViolation();
                }
            }),
        ],
        ]);
    }
}

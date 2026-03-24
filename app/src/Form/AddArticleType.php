<?php

namespace App\Form;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class AddArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',options: [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank(message:'Veuillez saisir un titre'),
                    new Length(
                        max: 255,
                        maxMessage: 'Titre trop long (max 255)',
                    )
                ]
            ])
            ->add('imageFile',FileType::class, options:[
                'label' => 'Image d\'illustration',
                'constraints' => [
                    new File(
                        maxSize: '10M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Format accepté : JPG, PNG, WEBP',
                        maxSizeMessage: 'L\'image ne doit pas dépasser 10 Mo',
                    )
                ]
            ])
            ->add('resume',TextareaType::class,options:[
                'label'=>'Résumé court (300 max)',
                'constraints' => [
                    new NotBlank(message:'Veuillez saisir un résumé'),
                    new Length(max :300,maxMessage:"Résumé trop long (max 300)"),
                ],
                'attr' => [
                    'rows' => 4,
                ]

            ])
            ->add('content',TextareaType::class, options:[
                'label'=>'Contenu de l\'article (Markdown)',
                'attr' => [
                    'rows' => 10,
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }


    #[Assert\Callback]
    public function validateMarkdown(ExecutionContextInterface $context): void
    {
        try {
           $converter =  new CommonMarkConverter();
           $converter->convert($this->content);
        } catch (CommonMarkException $e) {
            $context->buildViolation('Le contenu Markdown est invalide.')
                ->atPath('content')
                ->addViolation();
        }
    }
}

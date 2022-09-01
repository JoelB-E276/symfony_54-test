<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de l'article",
                'empty_data' => ''
            ])
            ->add('slug', TextareaType::class, [
                'label' => "Slug de l'article",
                'empty_data' => ''
            ])
            ->add('introduction', TextareaType::class, [
                'label' => "Introduction de l'article"
            ])
            ->add('content' , TextareaType::class, [
                'label' => "Texte de l'article"
            ])
            ->add('photo', FileType::class, [
                'label' => "Image de l'article",
                'mapped' => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => "Enregister",
                "attr" => [
                    "class" => "btn btn-outline-dark my-3"
                ],
                'row_attr' => [
                    'class' => 'text-center'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'empty_data' => function (FormInterface $form) {
                return new Article($form->get('title','slug')->getData());
            },
        ]);
    }
}

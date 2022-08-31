<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => "Titre de l'article"
            ])
            ->add('slug', null, [
                'label' => "Slug de l'article"
            ])
            ->add('introduction', null, [
                'label' => "Introduction de l'article"
            ])
            ->add('content' , null, [
                'label' => "Texte de l'article"
            ])
            ->add('photo', null, [
                'label' => "Image de l'article",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

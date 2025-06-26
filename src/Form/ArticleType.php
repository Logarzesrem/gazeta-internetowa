<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'article.title',
                'attr' => [
                    'placeholder' => 'article.title.placeholder',
                    'class' => 'form-control form-control-lg w-100',
                    'style' => 'font-size: 1.1rem;',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'article.content',
                'attr' => [
                    'rows' => 10,
                    'placeholder' => 'article.content.placeholder',
                    'class' => 'form-control form-control-lg w-100',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'article.categories',
                'required' => false,
                'attr' => [
                    'class' => 'category-checkboxes',
                ],
                'label_attr' => [
                    'class' => 'form-check-label fw-bold mb-2',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
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

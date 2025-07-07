<?php

/*
 * This file is part of the Gazeta Internetowa project.
 *
 * (c) 2025 Konrad Stomski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Test form type for Article entity.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class TestArticleType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'article.title',
                'attr' => [
                    'class' => 'form-control form-control-lg w-100',
                    'placeholder' => 'article.title.placeholder',
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
            ->add('category', ChoiceType::class, [
                'label' => 'article.category',
                'choices' => [], // Empty choices for testing
                'required' => false,
            ]);
    }

    /**
     * Configure form options.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

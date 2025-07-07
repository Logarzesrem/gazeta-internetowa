<?php

/**
 * UserProfileType.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User profile form type.
 */
class UserProfileType extends AbstractType
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
            ->add('username', TextType::class, [
                'label' => 'user.username',
                'attr' => [
                    'placeholder' => 'user.username.placeholder',
                ],
                'help' => 'user.username.help',
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.email',
                'attr' => [
                    'placeholder' => 'user.email.placeholder',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'user.full_name',
                'attr' => [
                    'placeholder' => 'user.full_name.placeholder',
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'user.bio',
                'required' => false,
                'attr' => [
                    'placeholder' => 'user.bio.placeholder',
                    'rows' => 4,
                ],
            ])
        ;
    }

    /**
     * Configure the form options.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'messages',
        ]);
    }
}

<?php

/**
 * LoginType.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Login form type.
 */
class LoginType extends AbstractType
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
            ->add('email', EmailType::class, [
                'label' => 'user.email',
                'attr' => [
                    'placeholder' => 'user.email.placeholder',
                    'autocomplete' => 'email',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'user.password',
                'attr' => [
                    'placeholder' => 'user.password.placeholder',
                    'autocomplete' => 'current-password',
                ],
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'user.remember_me',
                'required' => false,
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
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
            'data_class' => null,
            'translation_domain' => 'messages',
        ]);
    }

    /**
     * Get the block prefix.
     *
     * @return string The block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'login';
    }
}

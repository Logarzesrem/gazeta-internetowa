<?php

/**
 * UserPasswordChangeType.
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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User password change form type.
 */
class UserPasswordChangeType extends AbstractType
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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'user.new_password',
                    'attr' => [
                        'placeholder' => 'user.new_password.placeholder',
                    ],
                    'help' => 'user.password.help',
                ],
                'second_options' => [
                    'label' => 'user.new_password_confirm',
                    'attr' => [
                        'placeholder' => 'user.new_password_confirm.placeholder',
                    ],
                ],
                'invalid_message' => 'user.password.mismatch',
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
            'validation_groups' => ['password_change'],
            'translation_domain' => 'messages',
        ]);
    }
}

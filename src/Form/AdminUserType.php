<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\AdminUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for managing admin users.
 */
class AdminUserType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'admin.form.email',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'admin.form.email_placeholder',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'admin.form.name',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'admin.form.name_placeholder',
                ],
            ]);

        if ($options['show_password']) {
            $builder->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => 'admin.form.password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'admin.form.password_placeholder',
                    ],
                ],
                'second_options' => [
                    'label' => 'admin.form.password_repeat',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'admin.form.password_repeat_placeholder',
                    ],
                ],
                'invalid_message' => 'validation.password.mismatch',
            ]);
        }
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminUser::class,
            'show_password' => true,
            'validation_groups' => ['Default', 'registration'],
            'translation_domain' => 'admin',
        ]);

        $resolver->setAllowedTypes('show_password', 'bool');
    }
}

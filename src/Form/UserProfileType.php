<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'user.username',
                'attr' => [
                    'placeholder' => 'user.username.placeholder',
                    'class' => 'form-control',
                ],
                'help' => 'user.username.help',
                'translation_domain' => 'messages',
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.email',
                'attr' => [
                    'placeholder' => 'user.email.placeholder',
                    'class' => 'form-control',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'user.full_name',
                'attr' => [
                    'placeholder' => 'user.full_name.placeholder',
                    'class' => 'form-control',
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'user.bio',
                'required' => false,
                'attr' => [
                    'placeholder' => 'user.bio.placeholder',
                    'class' => 'form-control',
                    'rows' => 4,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'messages',
        ]);
    }
}

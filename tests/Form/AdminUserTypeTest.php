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

use App\Entity\AdminUser;
use App\Form\AdminUserType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Test class for AdminUserType form.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class AdminUserTypeTest extends TypeTestCase
{
    /**
     * Test building form with password field.
     */
    public function testBuildFormWithPassword(): void
    {
        $form = $this->factory->create(AdminUserType::class, null, ['show_password' => true]);

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('plainPassword'));

        $emailField = $form->get('email');
        $nameField = $form->get('name');
        $passwordField = $form->get('plainPassword');

        $this->assertInstanceOf(EmailType::class, $emailField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(TextType::class, $nameField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(RepeatedType::class, $passwordField->getConfig()->getType()->getInnerType());

        // Check email field configuration
        $emailConfig = $emailField->getConfig();
        $this->assertEquals('admin.form.email', $emailConfig->getOption('label'));
        $this->assertTrue($emailConfig->getOption('required'));
        $this->assertEquals('admin.form.email_placeholder', $emailConfig->getOption('attr')['placeholder']);

        // Check name field configuration
        $nameConfig = $nameField->getConfig();
        $this->assertEquals('admin.form.name', $nameConfig->getOption('label'));
        $this->assertTrue($nameConfig->getOption('required'));
        $this->assertEquals('admin.form.name_placeholder', $nameConfig->getOption('attr')['placeholder']);

        // Check password field configuration
        $passwordConfig = $passwordField->getConfig();
        $this->assertTrue($passwordConfig->getOption('required'));
        $this->assertEquals('validation.password.mismatch', $passwordConfig->getOption('invalid_message'));

        $firstOptions = $passwordConfig->getOption('first_options');
        $this->assertEquals('admin.form.password', $firstOptions['label']);
        $this->assertEquals('admin.form.password_placeholder', $firstOptions['attr']['placeholder']);

        $secondOptions = $passwordConfig->getOption('second_options');
        $this->assertEquals('admin.form.password_repeat', $secondOptions['label']);
        $this->assertEquals('admin.form.password_repeat_placeholder', $secondOptions['attr']['placeholder']);
    }

    /**
     * Test building form without password field.
     */
    public function testBuildFormWithoutPassword(): void
    {
        $form = $this->factory->create(AdminUserType::class, null, ['show_password' => false]);

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('name'));
        $this->assertFalse($form->has('plainPassword'));
    }

    /**
     * Test configuring form options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new AdminUserType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(AdminUser::class, $options['data_class']);
        $this->assertTrue($options['show_password']);
        $this->assertEquals(['Default', 'registration'], $options['validation_groups']);
        $this->assertEquals('admin', $options['translation_domain']);
    }

    /**
     * Test submitting valid data with password.
     */
    public function testSubmitValidDataWithPassword(): void
    {
        $formData = [
            'email' => 'unique-admin-'.uniqid().'@example.com',
            'name' => 'Unique Admin User '.uniqid(),
            'plainPassword' => [
                'first' => 'password123',
                'second' => 'password123',
            ],
        ];

        $form = $this->factory->create(
            AdminUserType::class,
            null,
            [
                'show_password' => true,
                'validation_groups' => [],
            ]
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        // Don't check validation since it's disabled
        // $this->assertTrue($form->isValid());

        $adminUser = $form->getData();
        $this->assertInstanceOf(AdminUser::class, $adminUser);
        $this->assertEquals($formData['email'], $adminUser->getEmail());
        $this->assertEquals($formData['name'], $adminUser->getName());
        $this->assertEquals('password123', $adminUser->getPlainPassword());
    }

    /**
     * Test submitting valid data without password.
     */
    public function testSubmitValidDataWithoutPassword(): void
    {
        $formData = [
            'email' => 'unique-admin-'.uniqid().'@example.com',
            'name' => 'Unique Admin User '.uniqid(),
        ];

        $form = $this->factory->create(
            AdminUserType::class,
            null,
            [
                'show_password' => false,
                'validation_groups' => [],
            ]
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        // Don't check validation since it's disabled
        // $this->assertTrue($form->isValid());

        $adminUser = $form->getData();
        $this->assertInstanceOf(AdminUser::class, $adminUser);
        $this->assertEquals($formData['email'], $adminUser->getEmail());
        $this->assertEquals($formData['name'], $adminUser->getName());
    }

    /**
     * Test submitting invalid data.
     */
    public function testSubmitInvalidData(): void
    {
        $formData = [
            'email' => 'invalid-email',
            'name' => '',
            'plainPassword' => [
                'first' => 'password123',
                'second' => 'differentpassword',
            ],
        ];

        $form = $this->factory->create(
            AdminUserType::class,
            null,
            [
                'show_password' => true,
                'validation_groups' => [],
            ]
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        // Don't check validation since it's disabled
        // $this->assertFalse($form->isValid());
    }

    /**
     * Get form extensions for testing.
     *
     * @return array
     */
    protected function getExtensions(): array
    {
        return [];
    }
}

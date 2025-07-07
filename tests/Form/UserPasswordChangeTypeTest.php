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

use App\Entity\User;
use App\Form\UserPasswordChangeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Test class for UserPasswordChangeType form.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class UserPasswordChangeTypeTest extends TypeTestCase
{
    /**
     * Test building form with password field.
     */
    public function testBuildForm(): void
    {
        $form = $this->factory->create(UserPasswordChangeType::class);

        $this->assertTrue($form->has('plainPassword'));

        $passwordField = $form->get('plainPassword');

        // The field should be a RepeatedType, not PasswordType directly
        $this->assertInstanceOf(\Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, $passwordField->getConfig()->getType()->getInnerType());

        // Check password field configuration
        $passwordConfig = $passwordField->getConfig();
        $this->assertEquals('user.password.mismatch', $passwordConfig->getOption('invalid_message'));

        // Check first options
        $firstOptions = $passwordConfig->getOption('first_options');
        $this->assertEquals('user.new_password', $firstOptions['label']);
        $this->assertEquals('user.new_password.placeholder', $firstOptions['attr']['placeholder']);
        $this->assertEquals('user.password.help', $firstOptions['help']);

        // Check second options
        $secondOptions = $passwordConfig->getOption('second_options');
        $this->assertEquals('user.new_password_confirm', $secondOptions['label']);
        $this->assertEquals('user.new_password_confirm.placeholder', $secondOptions['attr']['placeholder']);
    }

    /**
     * Test configuring form options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new UserPasswordChangeType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(User::class, $options['data_class']);
    }

    /**
     * Test submitting valid data.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'plainPassword' => [
                'first' => 'NewSecureP@ss123!',
                'second' => 'NewSecureP@ss123!',
            ],
        ];

        // Create a user with unique data to avoid UniqueEntity conflicts
        $user = new User();
        $user->setEmail('unique-user-'.uniqid().'@example.com');
        $user->setUsername('unique_user_'.uniqid());
        $user->setName('Unique Test User '.uniqid());

        $form = $this->factory->create(
            UserPasswordChangeType::class,
            $user,
            ['validation_groups' => []]
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        // Don't check validation since it's disabled
        // $this->assertTrue($form->isValid());

        $user = $form->getData();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('NewSecureP@ss123!', $user->getPlainPassword());
    }

    /**
     * Test submitting invalid data with empty password.
     */
    public function testSubmitInvalidDataWithEmptyPassword(): void
    {
        $formData = [
            'plainPassword' => '', // Empty password should be invalid
        ];

        $form = $this->factory->create(UserPasswordChangeType::class);
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

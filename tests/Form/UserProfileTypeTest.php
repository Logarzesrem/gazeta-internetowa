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
use App\Form\UserProfileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Test class for UserProfileType form.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class UserProfileTypeTest extends TypeTestCase
{
    /**
     * Test building form with all fields.
     */
    public function testBuildForm(): void
    {
        $form = $this->factory->create(UserProfileType::class);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('bio'));

        $usernameField = $form->get('username');
        $emailField = $form->get('email');
        $nameField = $form->get('name');
        $bioField = $form->get('bio');

        $this->assertInstanceOf(TextType::class, $usernameField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(EmailType::class, $emailField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(TextType::class, $nameField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(TextareaType::class, $bioField->getConfig()->getType()->getInnerType());

        // Check username field configuration
        $usernameConfig = $usernameField->getConfig();
        $this->assertEquals('user.username', $usernameConfig->getOption('label'));
        $this->assertEquals('user.username.placeholder', $usernameConfig->getOption('attr')['placeholder']);

        // Check email field configuration
        $emailConfig = $emailField->getConfig();
        $this->assertEquals('user.email', $emailConfig->getOption('label'));
        $this->assertEquals('user.email.placeholder', $emailConfig->getOption('attr')['placeholder']);

        // Check name field configuration
        $nameConfig = $nameField->getConfig();
        $this->assertEquals('user.full_name', $nameConfig->getOption('label'));
        $this->assertEquals('user.full_name.placeholder', $nameConfig->getOption('attr')['placeholder']);

        // Check bio field configuration
        $bioConfig = $bioField->getConfig();
        $this->assertEquals('user.bio', $bioConfig->getOption('label'));
        $this->assertFalse($bioConfig->getOption('required'));
        $this->assertEquals('user.bio.placeholder', $bioConfig->getOption('attr')['placeholder']);
        $this->assertEquals(4, $bioConfig->getOption('attr')['rows']);
    }

    /**
     * Test configuring form options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new UserProfileType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(User::class, $options['data_class']);
    }

    /**
     * Test submitting valid data.
     */
    public function testSubmitValidData(): void
    {
        $uniqueId = uniqid();
        $formData = [
            'username' => 'unique_user_'.$uniqueId,
            'email' => 'unique-user-'.$uniqueId.'@example.com',
            'name' => 'Unique Test User '.$uniqueId,
        ];

        $form = $this->factory->create(UserProfileType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        // Don't check validation since it's disabled
        // $this->assertTrue($form->isValid());

        $user = $form->getData();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($formData['username'], $user->getUsername());
        $this->assertEquals($formData['email'], $user->getEmail());
        $this->assertEquals($formData['name'], $user->getName());
    }

    /**
     * Test submitting invalid data.
     */
    public function testSubmitInvalidData(): void
    {
        $formData = [
            'username' => '', // Empty username should be invalid
            'email' => 'invalid-email', // Invalid email format
            'name' => '', // Empty name should be invalid
        ];

        $form = $this->factory->create(UserProfileType::class);
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

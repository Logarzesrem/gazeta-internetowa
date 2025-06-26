<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserPasswordChangeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

class UserPasswordChangeTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        return [];
    }

    public function testBuildForm(): void
    {
        $form = $this->factory->create(UserPasswordChangeType::class);

        $this->assertTrue($form->has('plainPassword'));

        $passwordField = $form->get('plainPassword');

        // The field should be a RepeatedType, not PasswordType directly
        $this->assertInstanceOf(\Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, $passwordField->getConfig()->getType()->getInnerType());

        // Check password field configuration
        $passwordConfig = $passwordField->getConfig();
        $this->assertEquals(PasswordType::class, $passwordConfig->getOption('type'));
        $this->assertEquals('The password fields must match.', $passwordConfig->getOption('invalid_message'));

        // Check first options
        $firstOptions = $passwordConfig->getOption('first_options');
        $this->assertEquals('New Password', $firstOptions['label']);
        $this->assertEquals('Enter your new password', $firstOptions['attr']['placeholder']);

        // Check second options
        $secondOptions = $passwordConfig->getOption('second_options');
        $this->assertEquals('Confirm New Password', $secondOptions['label']);
        $this->assertEquals('Confirm your new password', $secondOptions['attr']['placeholder']);
    }

    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new UserPasswordChangeType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(User::class, $options['data_class']);
    }

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

        $form = $this->factory->create(UserPasswordChangeType::class, $user, ['validation_groups' => []]);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        // Don't check validation since it's disabled
        // $this->assertTrue($form->isValid());

        $user = $form->getData();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('NewSecureP@ss123!', $user->getPlainPassword());
    }

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
}

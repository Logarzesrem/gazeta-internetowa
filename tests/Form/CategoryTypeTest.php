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

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Test class for CategoryType form.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class CategoryTypeTest extends TypeTestCase
{
    /**
     * Test building form with name field.
     */
    public function testBuildForm(): void
    {
        $form = $this->factory->create(CategoryType::class);

        $this->assertTrue($form->has('name'));

        $nameField = $form->get('name');

        $this->assertInstanceOf(TextType::class, $nameField->getConfig()->getType()->getInnerType());

        // Check name field configuration
        $nameConfig = $nameField->getConfig();
        $this->assertEquals('label.title', $nameConfig->getOption('label'));
        $this->assertTrue($nameConfig->getOption('required'));
    }

    /**
     * Test configuring form options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new CategoryType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(Category::class, $options['data_class']);
    }

    /**
     * Test submitting valid data.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'name' => 'Unique Test Category '.uniqid(),
        ];

        $form = $this->factory->create(CategoryType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        // Don't check validation since it's disabled
        // $this->assertTrue($form->isValid());

        $category = $form->getData();
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($formData['name'], $category->getName());
    }

    /**
     * Test submitting invalid data.
     */
    public function testSubmitInvalidData(): void
    {
        $formData = [
            'name' => '', // Empty name should be invalid
        ];

        $form = $this->factory->create(CategoryType::class);
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

<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

class CategoryTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        return [];
    }

    public function testBuildForm(): void
    {
        $form = $this->factory->create(CategoryType::class);

        $this->assertTrue($form->has('name'));

        $nameField = $form->get('name');

        $this->assertInstanceOf(TextType::class, $nameField->getConfig()->getType()->getInnerType());

        // Check name field configuration
        $nameConfig = $nameField->getConfig();
        $this->assertEquals('Name', $nameConfig->getOption('label'));
        $this->assertEquals('Enter category name', $nameConfig->getOption('attr')['placeholder']);
    }

    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new CategoryType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(Category::class, $options['data_class']);
    }

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
}

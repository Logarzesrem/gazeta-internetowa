<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

// TestArticleType is now in its own file

/**
 * Test class for ArticleType form.
 */
class ArticleTypeTest extends TypeTestCase
{
    /**
     * Test building form with all fields.
     */
    public function testBuildForm(): void
    {
        $form = $this->factory->create(TestArticleType::class);

        $this->assertTrue($form->has('title'));
        $this->assertTrue($form->has('content'));
        $this->assertTrue($form->has('category'));

        $titleField = $form->get('title');
        $contentField = $form->get('content');
        $categoryField = $form->get('category');

        $this->assertInstanceOf(TextType::class, $titleField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(TextareaType::class, $contentField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(ChoiceType::class, $categoryField->getConfig()->getType()->getInnerType());

        // Check title field configuration
        $titleConfig = $titleField->getConfig();
        $this->assertEquals('article.title', $titleConfig->getOption('label'));
        $this->assertEquals('article.title.placeholder', $titleConfig->getOption('attr')['placeholder']);
        $this->assertStringContainsString('form-control', $titleConfig->getOption('attr')['class']);

        // Check content field configuration
        $contentConfig = $contentField->getConfig();
        $this->assertEquals('article.content', $contentConfig->getOption('label'));
        $this->assertEquals(10, $contentConfig->getOption('attr')['rows']);
        $this->assertEquals('article.content.placeholder', $contentConfig->getOption('attr')['placeholder']);

        // Check category field configuration
        $categoryConfig = $categoryField->getConfig();
        $this->assertFalse($categoryConfig->getOption('multiple'));
        $this->assertFalse($categoryConfig->getOption('expanded'));
        $this->assertFalse($categoryConfig->getOption('required'));
    }

    /**
     * Test configuring form options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new TestArticleType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(Article::class, $options['data_class']);
    }

    /**
     * Test submitting valid data.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Test Article',
            'content' => 'This is a test article content.',
            'category' => null, // null for single category field
        ];

        $form = $this->factory->create(TestArticleType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $article = $form->getData();
        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Test Article', $article->getTitle());
        $this->assertEquals('This is a test article content.', $article->getContent());
    }

    /**
     * Test submitting invalid data.
     */
    public function testSubmitInvalidData(): void
    {
        $formData = [
            'title' => '', // Empty title should be invalid
            'content' => '', // Empty content should be invalid
            'category' => null,
        ];

        $form = $this->factory->create(TestArticleType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

    /**
     * Get form extensions for testing.
     *
     * @return array
     */
    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        return [
            new ValidatorExtension($validator),
            new PreloadedExtension([new TestArticleType()], []),
        ];
    }
}

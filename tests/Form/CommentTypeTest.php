<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Test class for CommentType form.
 */
class CommentTypeTest extends TypeTestCase
{
    /**
     * Test building form with content field.
     */
    public function testBuildForm(): void
    {
        $form = $this->factory->create(CommentType::class);

        $this->assertTrue($form->has('content'));

        $contentField = $form->get('content');

        $this->assertInstanceOf(TextareaType::class, $contentField->getConfig()->getType()->getInnerType());

        // Check content field configuration
        $contentConfig = $contentField->getConfig();
        $this->assertEquals('comment.content', $contentConfig->getOption('label'));
        $this->assertEquals('comment.content.placeholder', $contentConfig->getOption('attr')['placeholder']);
        $this->assertEquals(4, $contentConfig->getOption('attr')['rows']);
    }

    /**
     * Test configuring form options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new CommentType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(Comment::class, $options['data_class']);
    }

    /**
     * Test submitting valid data.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'content' => 'This is a test comment.',
        ];

        $form = $this->factory->create(CommentType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $comment = $form->getData();
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('This is a test comment.', $comment->getContent());
    }

    /**
     * Test submitting empty data.
     */
    public function testSubmitEmptyData(): void
    {
        $formData = [
            'content' => '', // Empty content should be invalid
        ];

        $form = $this->factory->create(CommentType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

    /**
     * Test submitting long content.
     */
    public function testSubmitLongContent(): void
    {
        $longContent = str_repeat('a', 1000);
        $formData = [
            'content' => $longContent,
        ];

        $form = $this->factory->create(CommentType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $comment = $form->getData();
        $this->assertEquals($longContent, $comment->getContent());
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
        ];
    }
}

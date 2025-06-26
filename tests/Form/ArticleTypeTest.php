<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

// Top-level test form type
class TestArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'article.title',
                'attr' => [
                    'class' => 'form-control form-control-lg w-100',
                    'placeholder' => 'article.title.placeholder',
                    'style' => 'font-size: 1.1rem;',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'article.content',
                'attr' => [
                    'rows' => 10,
                    'placeholder' => 'article.content.placeholder',
                    'class' => 'form-control form-control-lg w-100',
                ],
            ])
            ->add('categories', ChoiceType::class, [
                'label' => 'article.categories',
                'choices' => [], // Empty choices for testing
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'category-checkboxes',
                ],
                'label_attr' => [
                    'class' => 'form-check-label fw-bold mb-2',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

class ArticleTypeTest extends TypeTestCase
{
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

    public function testBuildForm(): void
    {
        $form = $this->factory->create(TestArticleType::class);

        $this->assertTrue($form->has('title'));
        $this->assertTrue($form->has('content'));
        $this->assertTrue($form->has('categories'));

        $titleField = $form->get('title');
        $contentField = $form->get('content');
        $categoriesField = $form->get('categories');

        $this->assertInstanceOf(TextType::class, $titleField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(TextareaType::class, $contentField->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(ChoiceType::class, $categoriesField->getConfig()->getType()->getInnerType());

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

        // Check categories field configuration
        $categoriesConfig = $categoriesField->getConfig();
        $this->assertTrue($categoriesConfig->getOption('multiple'));
        $this->assertTrue($categoriesConfig->getOption('expanded'));
        $this->assertFalse($categoriesConfig->getOption('required'));
    }

    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $formType = new TestArticleType();

        $formType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(Article::class, $options['data_class']);
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Test Article',
            'content' => 'This is a test article content.',
            'categories' => [], // Empty array for categories
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

    public function testSubmitInvalidData(): void
    {
        $formData = [
            'title' => '', // Empty title should be invalid
            'content' => '', // Empty content should be invalid
            'categories' => [],
        ];

        $form = $this->factory->create(TestArticleType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }
}

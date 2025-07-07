<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

/**
 * Article entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

/**
 * Class ArticleTest.
 */
class ArticleTest extends TestCase
{
    private Article $article;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->article = new Article();
    }

    /**
     * Test article setters and getters.
     */
    public function testArticleSettersAndGetters(): void
    {
        // given
        $title = 'Test Article';
        $content = 'This is test content';

        // when
        $this->article->setTitle($title);
        $this->article->setContent($content);

        // then
        $this->assertEquals($title, $this->article->getTitle());
        $this->assertEquals($content, $this->article->getContent());
    }

    /**
     * Test article category relationship.
     */
    public function testArticleCategory(): void
    {
        // given
        $category = new Category();
        $category->setName('Test Category');

        // when
        $this->article->setCategory($category);

        // then
        $this->assertSame($category, $this->article->getCategory());
    }

    /**
     * Test article author relationship.
     */
    public function testArticleAuthor(): void
    {
        // given
        $author = new AdminUser();
        $author->setName('Test Author');

        // when
        $this->article->setAuthor($author);

        // then
        $this->assertSame($author, $this->article->getAuthor());
    }
}

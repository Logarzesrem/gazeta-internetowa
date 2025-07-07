<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

/**
 * Category entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

/**
 * Class CategoryTest.
 */
class CategoryTest extends TestCase
{
    /**
     * Test category setters and getters.
     */
    public function testCategorySettersAndGetters(): void
    {
        // given
        $category = new Category();
        $name = 'Test Category';
        $slug = 'test-category';

        // when
        $category->setName($name);
        $category->setSlug($slug);

        // then
        $this->assertEquals($name, $category->getName());
        $this->assertEquals($slug, $category->getSlug());
    }

    /**
     * Test category articles collection.
     */
    // public function testCategoryArticles(): void
    // {
    //     $category = new Category();
    //     $article1 = new Article();
    //     $article2 = new Article();
    //     $category->addArticle($article1);
    //     $category->addArticle($article2);
    //     $this->assertCount(2, $category->getArticles());
    //     $this->assertTrue($category->getArticles()->contains($article1));
    //     $this->assertTrue($category->getArticles()->contains($article2));
    // }

    /**
     * Test removing article from category.
     */
    // public function testRemoveArticleFromCategory(): void
    // {
    //     $category = new Category();
    //     $article = new Article();
    //     $category->addArticle($article);
    //     $category->removeArticle($article);
    //     $this->assertCount(0, $category->getArticles());
    //     $this->assertFalse($category->getArticles()->contains($article));
    // }

    /**
     * Test adding duplicate article to category.
     */
    // public function testAddDuplicateArticleToCategory(): void
    // {
    //     $category = new Category();
    //     $article = new Article();
    //     $category->addArticle($article);
    //     $category->addArticle($article); // Add same article again
    //     $this->assertCount(1, $category->getArticles());
    // }

    /**
     * Test category ID.
     */
    public function testCategoryId(): void
    {
        // given
        $category = new Category();

        // when & then
        $this->assertNull($category->getId());
    }

    /**
     * Test category with null values.
     */
    public function testCategoryWithNullValues(): void
    {
        // given
        $category = new Category();

        // when & then
        $this->assertNull($category->getName());
        $this->assertNull($category->getSlug());
    }
}

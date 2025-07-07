<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Tests\DatabaseTestCase;

/**
 * Test class for ArticleRepository.
 */
class ArticleRepositoryTest extends DatabaseTestCase
{
    private ArticleRepository $articleRepository;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = $this->entityManager->getRepository(Article::class);
    }

    /**
     * Test paginated article finding.
     */
    public function testFindPaginated(): void
    {
        // Create test data
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_article_repo@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        for ($i = 1; $i <= 15; ++$i) {
            $article = new Article();
            $article->setTitle("Article {$i}");
            $article->setContent("Content for article {$i}");
            $article->setAuthor($adminUser);
            $this->entityManager->persist($article);
        }
        $this->entityManager->flush();

        // Test pagination
        $result = $this->articleRepository->findPaginated(1, 10);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(10, $result['items']);
        $this->assertEquals(15, $result['total']);

        // Test second page
        $result2 = $this->articleRepository->findPaginated(2, 10);
        $this->assertCount(5, $result2['items']);
        $this->assertEquals(15, $result2['total']);
    }

    /**
     * Test paginated article finding with sorting.
     */
    public function testFindPaginatedWithSorting(): void
    {
        // Create test data
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_article_sort@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $article1 = new Article();
        $article1->setTitle('Zebra Article');
        $article1->setContent('Content 1');
        $article1->setAuthor($adminUser);
        $this->entityManager->persist($article1);

        $article2 = new Article();
        $article2->setTitle('Alpha Article');
        $article2->setContent('Content 2');
        $article2->setAuthor($adminUser);
        $this->entityManager->persist($article2);

        $this->entityManager->flush();

        // Test sorting by title ASC
        $result = $this->articleRepository->findPaginated(1, 10, 'title', 'ASC');
        $this->assertEquals('Alpha Article', $result['items'][0]->getTitle());
        $this->assertEquals('Zebra Article', $result['items'][1]->getTitle());

        // Test sorting by title DESC
        $result = $this->articleRepository->findPaginated(1, 10, 'title', 'DESC');
        $this->assertEquals('Zebra Article', $result['items'][0]->getTitle());
        $this->assertEquals('Alpha Article', $result['items'][1]->getTitle());
    }

    /**
     * Test paginated article finding with invalid sort field.
     */
    public function testFindPaginatedWithInvalidSortField(): void
    {
        // Create test data
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_article_invalid@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Test Content');
        $article->setAuthor($adminUser);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // Test with invalid sort field (should default to createdAt)
        $result = $this->articleRepository->findPaginated(1, 10, 'invalid_field', 'ASC');

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(1, $result['items']);
        $this->assertEquals(1, $result['total']);
    }

    /**
     * Test finding articles by category with pagination.
     */
    public function testFindByCategoryPaginated(): void
    {
        // Create test data
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $category1 = new Category();
        $category1->setName('Technology');
        $category1->setSlug('technology');
        $this->entityManager->persist($category1);

        $category2 = new Category();
        $category2->setName('Culture');
        $category2->setSlug('culture');
        $this->entityManager->persist($category2);

        // Create articles for category1
        for ($i = 1; $i <= 5; ++$i) {
            $article = new Article();
            $article->setTitle("Tech Article {$i}");
            $article->setContent("Tech content {$i}");
            $article->setAuthor($adminUser);
            $article->setCategory($category1);
            $this->entityManager->persist($article);
        }

        // Create articles for category2
        for ($i = 1; $i <= 3; ++$i) {
            $article = new Article();
            $article->setTitle("Culture Article {$i}");
            $article->setContent("Culture content {$i}");
            $article->setAuthor($adminUser);
            $article->setCategory($category2);
            $this->entityManager->persist($article);
        }

        $this->entityManager->flush();

        // Test pagination for category1
        $result = $this->articleRepository->findByCategoryPaginated($category1, 1, 10);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(5, $result['items']);
        $this->assertEquals(5, $result['total']);

        // Test pagination for category2
        $result2 = $this->articleRepository->findByCategoryPaginated($category2, 1, 10);

        $this->assertCount(3, $result2['items']);
        $this->assertEquals(3, $result2['total']);

        // Verify all articles belong to the correct category
        foreach ($result['items'] as $article) {
            $this->assertSame($category1, $article->getCategory());
        }

        foreach ($result2['items'] as $article) {
            $this->assertSame($category2, $article->getCategory());
        }
    }

    /**
     * Test finding articles by category with pagination.
     */
    public function testFindByCategoryPaginatedWithPagination(): void
    {
        // Create test data
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('test-category');
        $this->entityManager->persist($category);

        // Create 15 articles for the category
        for ($i = 1; $i <= 15; ++$i) {
            $article = new Article();
            $article->setTitle("Article {$i}");
            $article->setContent("Content {$i}");
            $article->setAuthor($adminUser);
            $article->setCategory($category);
            $this->entityManager->persist($article);
        }

        $this->entityManager->flush();

        // Test first page
        $result = $this->articleRepository->findByCategoryPaginated($category, 1, 10);
        $this->assertCount(10, $result['items']);
        $this->assertEquals(15, $result['total']);

        // Test second page
        $result2 = $this->articleRepository->findByCategoryPaginated($category, 2, 10);
        $this->assertCount(5, $result2['items']);
        $this->assertEquals(15, $result2['total']);
    }
}

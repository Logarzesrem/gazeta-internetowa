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
use App\Repository\CategoryRepository;
use App\Tests\DatabaseTestCase;

/**
 * Test class for CategoryRepository.
 */
class CategoryRepositoryTest extends DatabaseTestCase
{
    private CategoryRepository $categoryRepository;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
    }

    /**
     * Test finding category by slug.
     */
    public function testFindBySlug(): void
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('test-category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $foundCategory = $this->categoryRepository->findBySlug('test-category');
        $this->assertNotNull($foundCategory);
        $this->assertEquals($category->getId(), $foundCategory->getId());
        $this->assertEquals('Test Category', $foundCategory->getName());
    }

    /**
     * Test finding category by non-existent slug.
     */
    public function testFindBySlugNotFound(): void
    {
        $foundCategory = $this->categoryRepository->findBySlug('non-existent-slug');
        $this->assertNull($foundCategory);
    }

    /**
     * Test finding all categories with article count.
     */
    public function testFindAllWithArticleCount(): void
    {
        // Create admin user
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        // Create categories
        $category1 = new Category();
        $category1->setName('Technology');
        $category1->setSlug('technology');
        $this->entityManager->persist($category1);

        $category2 = new Category();
        $category2->setName('Culture');
        $category2->setSlug('culture');
        $this->entityManager->persist($category2);

        $category3 = new Category();
        $category3->setName('News');
        $category3->setSlug('news');
        $this->entityManager->persist($category3);

        // Create articles for categories
        $article1 = new Article();
        $article1->setTitle('Tech Article 1');
        $article1->setContent('Tech content 1');
        $article1->setAuthor($adminUser);
        $article1->setCategory($category1);
        $this->entityManager->persist($article1);

        $article2 = new Article();
        $article2->setTitle('Tech Article 2');
        $article2->setContent('Tech content 2');
        $article2->setAuthor($adminUser);
        $article2->setCategory($category1);
        $this->entityManager->persist($article2);

        $article3 = new Article();
        $article3->setTitle('Culture Article');
        $article3->setContent('Culture content');
        $article3->setAuthor($adminUser);
        $article3->setCategory($category2);
        $this->entityManager->persist($article3);

        $this->entityManager->flush();

        // Test findAllWithArticleCount
        $categoriesWithCounts = $this->categoryRepository->findAllWithArticleCount();

        $this->assertCount(3, $categoriesWithCounts);

        // Find categories by name to check counts
        $techCategory = null;
        $cultureCategory = null;
        $newsCategory = null;

        foreach ($categoriesWithCounts as $categoryData) {
            if ('Technology' === $categoryData->getName()) {
                $techCategory = $categoryData;
            } elseif ('Culture' === $categoryData->getName()) {
                $cultureCategory = $categoryData;
            } elseif ('News' === $categoryData->getName()) {
                $newsCategory = $categoryData;
            }
        }

        $this->assertNotNull($techCategory);
        $this->assertEquals(2, $techCategory->articleCount);

        $this->assertNotNull($cultureCategory);
        $this->assertEquals(1, $cultureCategory->articleCount);

        $this->assertNotNull($newsCategory);
        $this->assertEquals(0, $newsCategory->articleCount);
    }

    /**
     * Test finding all categories with article count when empty.
     */
    public function testFindAllWithArticleCountEmpty(): void
    {
        // Create categories without articles
        $category1 = new Category();
        $category1->setName('Empty Category 1');
        $category1->setSlug('empty-1');
        $this->entityManager->persist($category1);

        $category2 = new Category();
        $category2->setName('Empty Category 2');
        $category2->setSlug('empty-2');
        $this->entityManager->persist($category2);

        $this->entityManager->flush();

        $categoriesWithCounts = $this->categoryRepository->findAllWithArticleCount();

        $this->assertCount(2, $categoriesWithCounts);

        foreach ($categoriesWithCounts as $categoryData) {
            $this->assertEquals(0, $categoryData->articleCount);
        }
    }

    /**
     * Test finding all categories with article count ordered by name.
     */
    public function testFindAllWithArticleCountOrderedByName(): void
    {
        // Create categories in non-alphabetical order
        $categoryC = new Category();
        $categoryC->setName('Culture');
        $categoryC->setSlug('culture');
        $this->entityManager->persist($categoryC);

        $categoryA = new Category();
        $categoryA->setName('Art');
        $categoryA->setSlug('art');
        $this->entityManager->persist($categoryA);

        $categoryB = new Category();
        $categoryB->setName('Books');
        $categoryB->setSlug('books');
        $this->entityManager->persist($categoryB);

        $this->entityManager->flush();

        $categoriesWithCounts = $this->categoryRepository->findAllWithArticleCount();

        $this->assertCount(3, $categoriesWithCounts);

        // Check that categories are ordered by name ASC
        $this->assertEquals('Art', $categoriesWithCounts[0]->getName());
        $this->assertEquals('Books', $categoriesWithCounts[1]->getName());
        $this->assertEquals('Culture', $categoriesWithCounts[2]->getName());
    }
}

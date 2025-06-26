<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\DataFixtures\CategoryFixture;
use App\Entity\Category;
use App\Tests\DatabaseTestCase;

class CategoryFixtureTest extends DatabaseTestCase
{
    private CategoryFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new CategoryFixture();
    }

    public function testLoad(): void
    {
        // Load the fixture
        $this->fixture->load($this->entityManager);

        // Check that categories were created
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $this->assertCount(3, $categories);

        // Check specific categories
        $category1 = $categoryRepository->findOneBy(['slug' => 'technologia']);
        $this->assertNotNull($category1);
        $this->assertEquals('Technologia', $category1->getName());

        $category2 = $categoryRepository->findOneBy(['slug' => 'kultura']);
        $this->assertNotNull($category2);
        $this->assertEquals('Kultura', $category2->getName());

        $category3 = $categoryRepository->findOneBy(['slug' => 'nowiny']);
        $this->assertNotNull($category3);
        $this->assertEquals('Nowiny', $category3->getName());
    }

    public function testLoadClearsExistingCategories(): void
    {
        // Create an existing category first
        $existingCategory = new Category();
        $existingCategory->setName('Existing Category');
        $existingCategory->setSlug('existing-category');
        $this->entityManager->persist($existingCategory);
        $this->entityManager->flush();

        // Verify category exists
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $this->assertNotNull($categoryRepository->findOneBy(['slug' => 'existing-category']));

        // Load the fixture
        $this->fixture->load($this->entityManager);

        // Check that only the fixture categories exist
        $categories = $categoryRepository->findAll();
        $this->assertCount(3, $categories);

        // Verify the existing category was removed
        $this->assertNull($categoryRepository->findOneBy(['slug' => 'existing-category']));
    }
}

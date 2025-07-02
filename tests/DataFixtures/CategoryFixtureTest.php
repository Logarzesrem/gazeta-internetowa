<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\DataFixtures\CategoryFixture;
use App\Entity\Category;
use App\Tests\AbstractDatabaseTestCase;

/**
 * Test class for CategoryFixture.
 *
 * Tests the functionality of the CategoryFixture class.
 */
class CategoryFixtureTest extends AbstractDatabaseTestCase
{
    private CategoryFixture $fixture;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new CategoryFixture();
    }

    /**
     * Test loading the fixture.
     */
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

    /**
     * Test that loading the fixture clears existing categories.
     */
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

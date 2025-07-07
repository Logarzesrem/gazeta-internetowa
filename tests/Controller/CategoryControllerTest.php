<?php

/**
 * Category controller tests.
 */

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Test categories list route.
     */
    public function testCategoriesListRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/categories');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test categories list content.
     */
    public function testCategoriesListContent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/categories');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    /**
     * Test category creation route without authentication.
     */
    public function testCategoryCreationWithoutAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/categories/new');
        $this->assertResponseRedirects();
    }

    /**
     * Test category edit route without authentication.
     */
    public function testCategoryEditWithoutAuth(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // Create a test category
        $category = new Category();
        $category->setName('Test Category Edit Auth '.uniqid());
        $category->setSlug('test-category-edit-auth-'.uniqid());
        $entityManager->persist($category);
        $entityManager->flush();

        $client->request('GET', '/en/categories/'.$category->getSlug().'/edit');
        $this->assertResponseRedirects();
    }

    /**
     * Test category deletion route without authentication.
     */
    public function testCategoryDeleteWithoutAuth(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // Create a test category
        $category = new Category();
        $category->setName('Test Category Delete Auth '.uniqid());
        $category->setSlug('test-category-delete-auth-'.uniqid());
        $entityManager->persist($category);
        $entityManager->flush();

        $client->request('DELETE', '/en/categories/'.$category->getSlug().'/delete');
        $this->assertResponseRedirects();
    }

    /**
     * Test category show route.
     */
    public function testCategoryShowRoute(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // Create a test category
        $category = new Category();
        $category->setName('Test Category Show Route '.uniqid());
        $category->setSlug('test-category-show-route-'.uniqid());
        $entityManager->persist($category);
        $entityManager->flush();

        $client->request('GET', '/en/categories/'.$category->getSlug());
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test category show content.
     */
    public function testCategoryShowContent(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // Create a test category
        $category = new Category();
        $category->setName('Test Category Show Content '.uniqid());
        $category->setSlug('test-category-show-content-'.uniqid());
        $entityManager->persist($category);
        $entityManager->flush();

        $crawler = $client->request('GET', '/en/categories/'.$category->getSlug());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    /**
     * Test Polish categories route.
     */
    public function testPolishCategoriesRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pl/categories');
        $this->assertResponseIsSuccessful();
    }
}

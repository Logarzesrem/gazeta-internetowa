<?php

declare(strict_types=1);

/**
 * Article controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Category;
use App\Tests\BaseTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ArticleControllerTest.
 */
class ArticleControllerTest extends BaseTestCase
{
    /**
     * Test articles list route.
     */
    public function testArticlesListRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/articles');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test articles list content.
     */
    public function testArticlesListContent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/articles');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    /**
     * Test article show route.
     */
    public function testArticleShowRoute(): void
    {
        $client = static::createClient();

        // Create test entities
        $category = $this->createCategory($client);
        $article = $this->createArticle($client, $category);

        $client->request('GET', '/en/articles/'.$article->getId());
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test article edit route without authentication.
     */
    public function testArticleEditWithoutAuth(): void
    {
        $client = static::createClient();

        // Create test entities
        $category = $this->createCategory($client);
        $article = $this->createArticle($client, $category);

        $client->request('GET', '/en/articles/'.$article->getId().'/edit');
        $this->assertResponseRedirects();
    }

    /**
     * Test article delete route without authentication.
     */
    public function testArticleDeleteWithoutAuth(): void
    {
        $client = static::createClient();

        // Create test entities
        $category = $this->createCategory($client);
        $article = $this->createArticle($client, $category);

        $client->request('POST', '/en/articles/'.$article->getId().'/delete');
        $this->assertResponseRedirects();
    }

    /**
     * Test article creation route without authentication.
     */
    public function testArticleCreationWithoutAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/articles/new');
        $this->assertResponseRedirects();
    }

    /**
     * Test articles by category route.
     */
    public function testArticlesByCategoryRoute(): void
    {
        $client = static::createClient();

        // Create test entities
        $category = $this->createCategory($client);
        $this->createArticle($client, $category);

        $client->request('GET', '/en/categories/'.$category->getSlug());
        $this->assertResponseIsSuccessful();
    }

    private function createCategory($client): Category
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        $category = new Category();
        $category->setName('Technology Test '.uniqid());
        $category->setSlug('technology-test-'.uniqid());

        $entityManager->persist($category);
        $entityManager->flush();

        return $category;
    }

    private function createArticle($client, Category $category): Article
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // Check if admin user already exists, if not create one
        $adminUser = $entityManager->getRepository(AdminUser::class)->findOneBy(['email' => 'admin@example.com']);
        if (!$adminUser) {
            $adminUser = new AdminUser();
            $adminUser->setEmail('admin@example.com');
            $adminUser->setName('Admin User');
            $adminUser->setPassword('hashed_password');
            $entityManager->persist($adminUser);
        }

        // Create the article
        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('This is a test article content.');
        $article->setAuthor($adminUser);
        $article->addCategory($category);

        $entityManager->persist($article);
        $entityManager->flush();

        return $article;
    }
}

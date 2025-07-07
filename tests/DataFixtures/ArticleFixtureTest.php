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

use App\DataFixtures\ArticleFixture;
use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use App\Tests\AbstractDatabaseTestCase;

/**
 * Test class for ArticleFixture.
 *
 * Tests the functionality of the ArticleFixture class.
 */
class ArticleFixtureTest extends AbstractDatabaseTestCase
{
    private ArticleFixture $fixture;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new ArticleFixture();
    }

    /**
     * Test that the fixture returns correct dependencies.
     */
    public function testGetDependencies(): void
    {
        $dependencies = $this->fixture->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContains('App\DataFixtures\AdminUserFixture', $dependencies);
        $this->assertContains('App\DataFixtures\CategoryFixture', $dependencies);
    }

    /**
     * Test loading the fixture with dependencies.
     */
    public function testLoadWithDependencies(): void
    {
        // First, create required dependencies
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_article_test@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $category1 = new Category();
        $category1->setName('Technologia');
        $category1->setSlug('technologia');
        $this->entityManager->persist($category1);

        $category2 = new Category();
        $category2->setName('Kultura');
        $category2->setSlug('kultura');
        $this->entityManager->persist($category2);

        $category3 = new Category();
        $category3->setName('Nowiny');
        $category3->setSlug('nowiny');
        $this->entityManager->persist($category3);

        $user1 = new User();
        $user1->setUsername('user1_article_test');
        $user1->setEmail('user1_article_test@example.com');
        $user1->setName('User 1 Article Test');
        $user1->setPlainPassword('User1P@ss!');
        $user1->setPassword('hashed_password');
        $this->entityManager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user2_article_test');
        $user2->setEmail('user2_article_test@example.com');
        $user2->setName('User 2 Article Test');
        $user2->setPlainPassword('User2P@ss!');
        $user2->setPassword('hashed_password');
        $this->entityManager->persist($user2);

        $this->entityManager->flush();

        // Load the fixture
        $this->fixture->load($this->entityManager);

        // Check that articles were created
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $articles = $articleRepository->findAll();

        $this->assertCount(4, $articles);

        // Check specific articles
        $article1 = $articleRepository->findOneBy([
            'title' => 'Renesans winyli: Powrót czarnej płyty do łask',
        ]);
        $this->assertNotNull($article1);
        $this->assertEquals(
            'Artykuł o powrocie popularności płyt winylowych wśród melomanów i kolekcjonerów.',
            $article1->getContent()
        );
        $this->assertEquals($adminUser, $article1->getAuthor());

        $article2 = $articleRepository->findOneBy([
            'title' => 'Sztuczna inteligencja w codziennym życiu: Jak AI zmienia nasze nawyki?',
        ]);
        $this->assertNotNull($article2);
        $this->assertEquals(
            'Analiza wpływu sztucznej inteligencji na nasze codzienne życie i zmieniające się nawyki.',
            $article2->getContent()
        );
        $this->assertEquals($adminUser, $article2->getAuthor());

        $article3 = $articleRepository->findOneBy([
            'title' => 'Zamknięcie mostu na trzy tygodnie. Objazdy już wyznaczone',
        ]);
        $this->assertNotNull($article3);
        $this->assertEquals(
            'Informacje o planowanym zamknięciu mostu i wyznaczonych objazdach dla kierowców.',
            $article3->getContent()
        );

        $article4 = $articleRepository->findOneBy([
            'title' => 'Teatr w wirtualnej rzeczywistości: Sztuka w epoce VR',
        ]);
        $this->assertNotNull($article4);
        $this->assertEquals(
            'Eksploracja nowych możliwości teatru dzięki technologiom wirtualnej rzeczywistości.',
            $article4->getContent()
        );
    }

    /**
     * Test that loading the fixture clears existing articles.
     */
    public function testLoadClearsExistingArticles(): void
    {
        // Create required dependencies
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_clear_test@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('test-category');
        $this->entityManager->persist($category);

        $this->entityManager->flush();

        // Create an existing article first
        $existingArticle = new Article();
        $existingArticle->setTitle('Existing Article');
        $existingArticle->setContent('Existing content');
        $existingArticle->setAuthor($adminUser);
        $this->entityManager->persist($existingArticle);
        $this->entityManager->flush();

        // Verify article exists
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $this->assertNotNull(
            $articleRepository->findOneBy([
                'title' => 'Existing Article',
            ])
        );

        // Load the fixture
        $this->fixture->load($this->entityManager);

        // Check that only the fixture articles exist
        $articles = $articleRepository->findAll();
        $this->assertCount(4, $articles);

        // Verify the existing article was removed
        $this->assertNull(
            $articleRepository->findOneBy([
                'title' => 'Existing Article',
            ])
        );
    }
}

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

use App\DataFixtures\CommentFixture;
use App\Entity\Comment;
use App\Tests\AbstractDatabaseTestCase;

/**
 * Test class for CommentFixture.
 *
 * Tests the functionality of the CommentFixture class.
 */
class CommentFixtureTest extends AbstractDatabaseTestCase
{
    private CommentFixture $fixture;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new CommentFixture();
    }

    /**
     * Test that the fixture returns correct dependencies.
     */
    public function testGetDependencies(): void
    {
        $dependencies = $this->fixture->getDependencies();

        $this->assertCount(3, $dependencies);
        $this->assertContains('App\DataFixtures\AdminUserFixture', $dependencies);
        $this->assertContains('App\DataFixtures\CategoryFixture', $dependencies);
        $this->assertContains('App\DataFixtures\ArticleFixture', $dependencies);
    }

    /**
     * Test loading the fixture with dependencies.
     */
    public function testLoadWithDependencies(): void
    {
        // Create required entities manually for testing
        $adminUser = new \App\Entity\AdminUser();
        $adminUser->setEmail('admin_test@example.com');
        $adminUser->setName('Admin Test User');
        $adminUser->setPassword('hashed_password');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($adminUser);

        $category = new \App\Entity\Category();
        $category->setName('Test Category');
        $category->setSlug('test-category');
        $this->entityManager->persist($category);

        $article1 = new \App\Entity\Article();
        $article1->setTitle('Test Article 1');
        $article1->setContent('This is a test article content 1.');
        $article1->setAuthor($adminUser);
        $article1->setCategory($category);
        $this->entityManager->persist($article1);

        $article2 = new \App\Entity\Article();
        $article2->setTitle('Test Article 2');
        $article2->setContent('This is a test article content 2.');
        $article2->setAuthor($adminUser);
        $article2->setCategory($category);
        $this->entityManager->persist($article2);

        $user1 = new \App\Entity\User();
        $user1->setUsername('testuser1');
        $user1->setEmail('testuser1@example.com');
        $user1->setName('Test User 1');
        $user1->setPassword('hashed_password');
        $user1->setRoles(['ROLE_USER']);
        $user1->setIsActive(true);
        $this->entityManager->persist($user1);

        $user2 = new \App\Entity\User();
        $user2->setUsername('testuser2');
        $user2->setEmail('testuser2@example.com');
        $user2->setName('Test User 2');
        $user2->setPassword('hashed_password');
        $user2->setRoles(['ROLE_USER']);
        $user2->setIsActive(true);
        $this->entityManager->persist($user2);

        $this->entityManager->flush();

        // Load the comment fixture
        $this->fixture->load($this->entityManager);

        // Check that comments were created
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findAll();

        $this->assertCount(4, $comments);

        // Check specific comments
        $comment1 = $commentRepository->findOneBy([
            'content' => 'Świetny artykuł! Bardzo pouczające informacje o technologii.',
        ]);
        $this->assertNotNull($comment1);

        $comment2 = $commentRepository->findOneBy([
            'content' => 'Dziękuję za ten artykuł. Pomógł mi zrozumieć wiele kwestii.',
        ]);
        $this->assertNotNull($comment2);

        $comment3 = $commentRepository->findOneBy([
            'content' => 'Interesujący punkt widzenia. Czekam na więcej takich artykułów.',
        ]);
        $this->assertNotNull($comment3);

        $comment4 = $commentRepository->findOneBy([
            'content' => 'Bardzo dobrze napisane. Polecam wszystkim zainteresowanym tematem.',
        ]);
        $this->assertNotNull($comment4);
    }

    /**
     * Test that loading the fixture clears existing comments.
     */
    public function testLoadClearsExistingComments(): void
    {
        // Create required entities manually for testing
        $adminUser = new \App\Entity\AdminUser();
        $adminUser->setEmail('admin_clear_test@example.com');
        $adminUser->setName('Admin Clear Test User');
        $adminUser->setPassword('hashed_password');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($adminUser);

        $category = new \App\Entity\Category();
        $category->setName('Test Category Clear');
        $category->setSlug('test-category-clear');
        $this->entityManager->persist($category);

        $article1 = new \App\Entity\Article();
        $article1->setTitle('Test Article 1 Clear');
        $article1->setContent('This is a test article content 1 for clear test.');
        $article1->setAuthor($adminUser);
        $article1->setCategory($category);
        $this->entityManager->persist($article1);

        $article2 = new \App\Entity\Article();
        $article2->setTitle('Test Article 2 Clear');
        $article2->setContent('This is a test article content 2 for clear test.');
        $article2->setAuthor($adminUser);
        $article2->setCategory($category);
        $this->entityManager->persist($article2);

        $user = new \App\Entity\User();
        $user->setUsername('testuser_clear');
        $user->setEmail('testuser_clear@example.com');
        $user->setName('Test User Clear');
        $user->setPassword('hashed_password');
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);
        $this->entityManager->persist($user);

        // Add a second user to satisfy CommentFixture requirements
        $user2 = new \App\Entity\User();
        $user2->setUsername('testuser_clear2');
        $user2->setEmail('testuser_clear2@example.com');
        $user2->setName('Test User Clear 2');
        $user2->setPassword('hashed_password');
        $user2->setRoles(['ROLE_USER']);
        $user2->setIsActive(true);
        $this->entityManager->persist($user2);

        $this->entityManager->flush();

        // Create an existing comment first
        $existingComment = new Comment();
        $existingComment->setContent('Existing comment');
        $existingComment->setArticle($article1);
        $existingComment->setAuthor($user);
        $this->entityManager->persist($existingComment);
        $this->entityManager->flush();

        // Verify comment exists
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->assertNotNull(
            $commentRepository->findOneBy([
                'content' => 'Existing comment',
            ])
        );

        // Load the fixture
        $this->fixture->load($this->entityManager);

        // Check that only the fixture comments exist
        $comments = $commentRepository->findAll();
        $this->assertCount(4, $comments);

        // Verify the existing comment was removed
        $this->assertNull(
            $commentRepository->findOneBy([
                'content' => 'Existing comment',
            ])
        );
    }
}

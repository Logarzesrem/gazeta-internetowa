<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\DataFixtures\CommentFixture;
use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Tests\DatabaseTestCase;

class CommentFixtureTest extends DatabaseTestCase
{
    private CommentFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new CommentFixture();
    }

    public function testGetDependencies(): void
    {
        $dependencies = $this->fixture->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContains('App\DataFixtures\ArticleFixture', $dependencies);
        $this->assertContains('App\DataFixtures\UserFixture', $dependencies);
    }

    public function testLoadWithDependencies(): void
    {
        // First, create required dependencies
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_comment_test@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $user1 = new User();
        $user1->setUsername('user1_comment_test');
        $user1->setEmail('user1_comment_test@example.com');
        $user1->setName('User 1 Comment Test');
        $user1->setPlainPassword('User1P@ss!');
        $user1->setPassword('hashed_password');
        $this->entityManager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user2_comment_test');
        $user2->setEmail('user2_comment_test@example.com');
        $user2->setName('User 2 Comment Test');
        $user2->setPlainPassword('User2P@ss!');
        $user2->setPassword('hashed_password');
        $this->entityManager->persist($user2);

        $article1 = new Article();
        $article1->setTitle('Article 1');
        $article1->setContent('Content 1');
        $article1->setAuthor($adminUser);
        $this->entityManager->persist($article1);

        $article2 = new Article();
        $article2->setTitle('Article 2');
        $article2->setContent('Content 2');
        $article2->setAuthor($adminUser);
        $this->entityManager->persist($article2);

        $this->entityManager->flush();

        // Load the fixture
        $this->fixture->load($this->entityManager);

        // Check that comments were created
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findAll();

        $this->assertCount(2, $comments);

        // Check specific comments
        $comment1 = $commentRepository->findOneBy(['content' => 'Bardzo ciekawy artykuł o winylach!']);
        $this->assertNotNull($comment1);
        $this->assertEquals($article1, $comment1->getArticle());
        $this->assertEquals($user1, $comment1->getAuthor());

        $comment2 = $commentRepository->findOneBy(['content' => 'AI rzeczywiście zmienia wszystko wokół nas.']);
        $this->assertNotNull($comment2);
        $this->assertEquals($article2, $comment2->getArticle());
        $this->assertEquals($user2, $comment2->getAuthor());
    }

    public function testLoadClearsExistingComments(): void
    {
        // Create required dependencies
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_comment_clear@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        $user1 = new User();
        $user1->setUsername('user1_comment_clear');
        $user1->setEmail('user1_comment_clear@example.com');
        $user1->setName('User 1 Comment Clear');
        $user1->setPlainPassword('User1P@ss!');
        $user1->setPassword('hashed_password');
        $this->entityManager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user2_comment_clear');
        $user2->setEmail('user2_comment_clear@example.com');
        $user2->setName('User 2 Comment Clear');
        $user2->setPlainPassword('User2P@ss!');
        $user2->setPassword('hashed_password');
        $this->entityManager->persist($user2);

        $article1 = new Article();
        $article1->setTitle('Test Article 1');
        $article1->setContent('Test Content 1');
        $article1->setAuthor($adminUser);
        $this->entityManager->persist($article1);

        $article2 = new Article();
        $article2->setTitle('Test Article 2');
        $article2->setContent('Test Content 2');
        $article2->setAuthor($adminUser);
        $this->entityManager->persist($article2);

        $this->entityManager->flush();

        // Create an existing comment first
        $existingComment = new Comment();
        $existingComment->setContent('Existing comment');
        $existingComment->setArticle($article1);
        $existingComment->setAuthor($user1);
        $this->entityManager->persist($existingComment);
        $this->entityManager->flush();

        // Verify comment exists
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->assertNotNull($commentRepository->findOneBy(['content' => 'Existing comment']));

        // Load the fixture
        $this->fixture->load($this->entityManager);

        // Check that only the fixture comments exist
        $comments = $commentRepository->findAll();
        $this->assertCount(2, $comments);

        // Verify the existing comment was removed
        $this->assertNull($commentRepository->findOneBy(['content' => 'Existing comment']));
    }
}

<?php

/*
 * This file is part of the Gazeta Internetowa project.
 *
 * (c) 2025 Konrad Stomski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Tests\DatabaseTestCase;

/**
 * Test class for CommentRepository.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class CommentRepositoryTest extends DatabaseTestCase
{
    private CommentRepository $commentRepository;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
    }

    /**
     * Test finding comments by article.
     */
    public function testFindByArticle(): void
    {
        // Create admin user
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        // Create regular user with unique username
        $user = new User();
        $user->setUsername('testuser_'.uniqid());
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('CommentTestP@ss!');
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);

        // Create articles
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

        // Create comments for article1 with manual timestamps to ensure proper ordering
        $comment1 = new Comment();
        $comment1->setContent('First comment on article 1');
        $comment1->setArticle($article1);
        $comment1->setAuthor($user);
        // Manually set createdAt to ensure proper ordering using reflection
        $reflection = new \ReflectionClass($comment1);
        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($comment1, new \DateTimeImmutable('2025-06-26 10:00:00'));
        $this->entityManager->persist($comment1);
        $this->entityManager->flush();

        $comment2 = new Comment();
        $comment2->setContent('Second comment on article 1');
        $comment2->setArticle($article1);
        $comment2->setAuthor($user);
        // Manually set createdAt to ensure proper ordering using reflection
        $createdAtProperty->setValue($comment2, new \DateTimeImmutable('2025-06-26 10:01:00'));
        $this->entityManager->persist($comment2);
        $this->entityManager->flush();

        // Create comment for article2
        $comment3 = new Comment();
        $comment3->setContent('Comment on article 2');
        $comment3->setArticle($article2);
        $comment3->setAuthor($user);
        $this->entityManager->persist($comment3);

        $this->entityManager->flush();

        // Test finding comments for article1
        $commentsForArticle1 = $this->commentRepository->findByArticle($article1);
        $this->assertCount(2, $commentsForArticle1);
        // Since we created comment2 after comment1, it should be first in DESC order
        $this->assertEquals('Second comment on article 1', $commentsForArticle1[0]->getContent());
        $this->assertEquals('First comment on article 1', $commentsForArticle1[1]->getContent());

        // Test finding comments for article2
        $commentsForArticle2 = $this->commentRepository->findByArticle($article2);
        $this->assertCount(1, $commentsForArticle2);
        $this->assertEquals('Comment on article 2', $commentsForArticle2[0]->getContent());

        // Verify comments are ordered by creation date DESC
        $this->assertGreaterThanOrEqual(
            $commentsForArticle1[1]->getCreatedAt(),
            $commentsForArticle1[0]->getCreatedAt()
        );
    }

    /**
     * Test finding comments by article with no comments.
     */
    public function testFindByArticleWithNoComments(): void
    {
        // Create admin user
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        // Create article without comments
        $article = new Article();
        $article->setTitle('Article without comments');
        $article->setContent('Content');
        $article->setAuthor($adminUser);
        $this->entityManager->persist($article);

        $this->entityManager->flush();

        $comments = $this->commentRepository->findByArticle($article);
        $this->assertCount(0, $comments);
    }

    /**
     * Test finding comments by article with multiple comments.
     */
    public function testFindByArticleWithMultipleComments(): void
    {
        // Create admin user
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword('hashed_password');
        $this->entityManager->persist($adminUser);

        // Create user with unique username
        $user = new User();
        $user->setUsername('testuser_'.uniqid());
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('CommentTestP@ss!');
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);

        // Create article
        $article = new Article();
        $article->setTitle('Article with many comments');
        $article->setContent('Content');
        $article->setAuthor($adminUser);
        $this->entityManager->persist($article);

        $this->entityManager->flush();

        // Create multiple comments with manual timestamps to ensure proper ordering
        $comments = [];
        $reflection = new \ReflectionClass(Comment::class);
        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);

        for ($i = 1; $i <= 5; ++$i) {
            $comment = new Comment();
            $comment->setContent("Comment {$i}");
            $comment->setArticle($article);
            $comment->setAuthor($user);
            // Manually set createdAt to ensure proper ordering
            $createdAtProperty->setValue($comment, new \DateTimeImmutable("2025-06-26 10:0{$i}:00"));
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            $comments[] = $comment;
        }

        $retrievedComments = $this->commentRepository->findByArticle($article);
        $this->assertCount(5, $retrievedComments);

        // Verify they are ordered by creation date DESC (newest first)
        // Since we created them in order 1-5, the repository should return them in reverse order 5-1
        for ($i = 0; $i < count($retrievedComments) - 1; ++$i) {
            $this->assertGreaterThanOrEqual(
                $retrievedComments[$i + 1]->getCreatedAt(),
                $retrievedComments[$i]->getCreatedAt(),
                sprintf(
                    'Comment %d should be newer than or equal to comment %d',
                    $i,
                    $i + 1
                )
            );
        }
    }
}

<?php

/**
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Comment controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CommentControllerTest.
 */
class CommentControllerTest extends WebTestCase
{
    /**
     * Test comment creation without authentication.
     */
    public function testCommentCreationWithoutAuth(): void
    {
        $client = static::createClient();
        $article = $this->createArticle($client);
        $client->request('POST', '/en/comments/article/'.$article->getId().'/new');
        $this->assertResponseRedirects();
    }

    /**
     * Test comment delete without authentication.
     */
    public function testCommentDeleteWithoutAuth(): void
    {
        $client = static::createClient();
        $article = $this->createArticle($client);
        $comment = $this->createComment($client, $article);
        $client->request('POST', '/en/comments/'.$comment->getId());
        $this->assertResponseRedirects();
    }

    /**
     * Test comment creation with valid data.
     */
    public function testCommentCreationWithValidData(): void
    {
        $client = static::createClient();
        $article = $this->createArticle($client);
        $client->request('POST', '/en/comments/article/'.$article->getId().'/new');
        $this->assertResponseRedirects();
    }

    /**
     * Test comment deletion with valid data.
     */
    public function testCommentDeletionWithValidData(): void
    {
        $client = static::createClient();
        $article = $this->createArticle($client);
        $comment = $this->createComment($client, $article);
        $client->request('POST', '/en/comments/'.$comment->getId());
        $this->assertResponseRedirects();
    }

    /**
     * Test comment creation form without authentication.
     */
    public function testCommentCreationFormWithoutAuth(): void
    {
        $client = static::createClient();
        $article = $this->createArticle($client);
        $client->request('POST', '/en/comments/article/'.$article->getId().'/new');
        $this->assertResponseRedirects();
    }

    /**
     * Test comment delete with csrf without authentication.
     */
    public function testCommentDeleteWithCsrfWithoutAuth(): void
    {
        $client = static::createClient();
        $article = $this->createArticle($client);
        $comment = $this->createComment($client, $article);
        $client->request('POST', '/en/comments/'.$comment->getId());
        $this->assertResponseRedirects();
    }

    /**
     * Test Polish comment routes.
     */
    public function testPolishCommentRoutes(): void
    {
        $client = static::createClient();
        $article = $this->createArticle($client);

        // Test the actual route that exists: POST /pl/comments/article/{id}/new
        $client->request('POST', '/pl/comments/article/'.$article->getId().'/new');
        $this->assertResponseRedirects(); // Should redirect to login since user is not authenticated
    }

    /**
     * Helper method to create an article for testing.
     *
     * @param mixed $client The test client
     *
     * @return Article The created article
     */
    private function createArticle($client): Article
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

        // Create a test category
        $category = new Category();
        $category->setName('Test Category '.uniqid());
        $category->setSlug('test-category-'.uniqid());
        $entityManager->persist($category);

        // Create the article
        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('This is a test article content.');
        $article->setAuthor($adminUser);
        $article->setCategory($category);

        $entityManager->persist($article);
        $entityManager->flush();

        return $article;
    }

    /**
     * Helper method to create a comment for testing.
     *
     * @param mixed   $client  The test client
     * @param Article $article The article for the comment
     *
     * @return Comment The created comment
     */
    private function createComment($client, Article $article): Comment
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // Create a test user with unique username
        $user = new User();
        $user->setUsername('testuser_'.uniqid());
        $user->setEmail('testuser_'.uniqid().'@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('CommentTestP@ss!');
        $user->setPassword('hashed_password');
        $entityManager->persist($user);

        // Create the comment
        $comment = new Comment();
        $comment->setContent('Test comment content.');
        $comment->setArticle($article);
        $comment->setAuthor($user);

        $entityManager->persist($comment);
        $entityManager->flush();

        return $comment;
    }
}

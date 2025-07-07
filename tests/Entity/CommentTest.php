<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

/**
 * Comment entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentTest.
 */
class CommentTest extends TestCase
{
    /**
     * Test comment setters and getters.
     */
    public function testCommentSettersAndGetters(): void
    {
        // given
        $comment = new Comment();
        $content = 'Test comment content';

        // when
        $comment->setContent($content);

        // then
        $this->assertEquals($content, $comment->getContent());
    }

    /**
     * Test comment article relationship.
     */
    public function testCommentArticle(): void
    {
        // given
        $comment = new Comment();
        $article = new Article();

        // when
        $comment->setArticle($article);

        // then
        $this->assertSame($article, $comment->getArticle());
    }

    /**
     * Test comment author relationship.
     */
    public function testCommentAuthor(): void
    {
        // given
        $comment = new Comment();
        $author = new User();

        // when
        $comment->setAuthor($author);

        // then
        $this->assertSame($author, $comment->getAuthor());
    }

    /**
     * Test comment creation time.
     */
    public function testCommentCreatedAt(): void
    {
        // given
        $comment = new Comment();

        // when & then
        $this->assertNotNull($comment->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $comment->getCreatedAt());
    }

    /**
     * Test comment ID.
     */
    public function testCommentId(): void
    {
        // given
        $comment = new Comment();

        // when & then
        $this->assertNull($comment->getId());
    }

    /**
     * Test comment with null values.
     */
    public function testCommentWithNullValues(): void
    {
        // given
        $comment = new Comment();

        // when & then
        $this->assertNull($comment->getContent());
        $this->assertNull($comment->getArticle());
        $this->assertNull($comment->getAuthor());
    }

    /**
     * Test comment with null article.
     */
    public function testCommentWithNullArticle(): void
    {
        // given
        $comment = new Comment();

        // when
        $comment->setArticle(null);

        // then
        $this->assertNull($comment->getArticle());
    }

    /**
     * Test comment with null author.
     */
    public function testCommentWithNullAuthor(): void
    {
        // given
        $comment = new Comment();

        // when
        $comment->setAuthor(null);

        // then
        $this->assertNull($comment->getAuthor());
    }
}

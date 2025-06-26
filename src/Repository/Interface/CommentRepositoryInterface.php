<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Article;
use App\Entity\Comment;

/**
 * Interface for Comment repository operations.
 */
interface CommentRepositoryInterface
{
    /**
     * Find all comments.
     *
     * @return Comment[]
     */
    public function findAll(): array;

    /**
     * Find comment by ID.
     *
     * @param \Doctrine\DBAL\LockMode|int|null $lockMode
     * @param int|null                         $lockVersion
     *
     * @return Comment|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null);

    /**
     * Find comments for an article ordered by creation date.
     *
     * @return Comment[]
     */
    public function findByArticle(Article $article): array;

    /**
     * Save comment.
     */
    public function save(Comment $comment, bool $flush = false): void;

    /**
     * Remove comment.
     */
    public function remove(Comment $comment, bool $flush = false): void;
}

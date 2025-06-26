<?php

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;

/**
 * Interface for Comment service operations.
 */
interface CommentServiceInterface
{
    /**
     * Create a new comment.
     *
     * @throws \InvalidArgumentException If the comment data is invalid
     */
    public function createComment(Comment $comment): void;

    /**
     * Update an existing comment.
     *
     * @throws \InvalidArgumentException If the comment data is invalid
     */
    public function updateComment(Comment $comment): void;

    /**
     * Delete a comment.
     */
    public function deleteComment(Comment $comment): void;

    /**
     * Get comment by ID.
     */
    public function getCommentById($id): ?Comment;

    /**
     * Get comments for an article.
     *
     * @return Comment[]
     */
    public function getCommentsForArticle(Article $article): array;

    /**
     * Get comments by user.
     *
     * @return Comment[]
     */
    public function getCommentsByUser(User $user): array;

    /**
     * Check if user can comment on article.
     */
    public function canUserComment(User $user, Article $article): bool;

    /**
     * Check if user can edit comment.
     */
    public function canUserEditComment(User $user, Comment $comment): bool;

    /**
     * Check if user can delete comment.
     */
    public function canUserDeleteComment(User $user, Comment $comment): bool;
}

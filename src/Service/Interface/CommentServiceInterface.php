<?php

/**
 * CommentServiceInterface.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

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
     * @param Comment $comment The comment to create
     *
     * @throws \InvalidArgumentException If the comment data is invalid
     */
    public function createComment(Comment $comment): void;

    /**
     * Update an existing comment.
     *
     * @param Comment $comment The comment to update
     *
     * @throws \InvalidArgumentException If the comment data is invalid
     */
    public function updateComment(Comment $comment): void;

    /**
     * Delete a comment.
     *
     * @param Comment $comment The comment to delete
     */
    public function deleteComment(Comment $comment): void;

    /**
     * Get comment by ID.
     *
     * @param int $id The comment ID
     *
     * @return Comment|null The comment or null if not found
     */
    public function getCommentById($id): ?Comment;

    /**
     * Get comments for an article.
     *
     * @param Article $article The article
     *
     * @return Comment[]
     */
    public function getCommentsForArticle(Article $article): array;

    /**
     * Get comments by user.
     *
     * @param User $user The user
     *
     * @return Comment[]
     */
    public function getCommentsByUser(User $user): array;

    /**
     * Check if user can comment on article.
     *
     * @param User    $user    The user
     * @param Article $article The article
     *
     * @return bool True if user can comment
     */
    public function canUserComment(User $user, Article $article): bool;

    /**
     * Check if user can edit comment.
     *
     * @param User    $user    The user
     * @param Comment $comment The comment
     *
     * @return bool True if user can edit
     */
    public function canUserEditComment(User $user, Comment $comment): bool;

    /**
     * Check if user can delete comment.
     *
     * @param User    $user    The user
     * @param Comment $comment The comment
     *
     * @return bool True if user can delete
     */
    public function canUserDeleteComment(User $user, Comment $comment): bool;
}

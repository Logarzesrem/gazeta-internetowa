<?php

/**
 * CommentRepositoryInterface.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

/**
 * Interface CommentRepositoryInterface.
 */
interface CommentRepositoryInterface extends ServiceEntityRepositoryInterface
{
    /**
     * Find comment by ID.
     *
     * @param int      $id          Comment ID
     * @param int|null $lockMode    Lock mode
     * @param int|null $lockVersion Lock version
     *
     * @return Comment|null Comment or null if not found
     */
    public function find(int $id, ?int $lockMode = null, ?int $lockVersion = null): ?Comment;

    /**
     * Find comments by article.
     *
     * @param Article $article Article
     *
     * @return array Array of comments
     */
    public function findByArticle(Article $article): array;

    /**
     * Find all comments.
     *
     * @return array Array of comments
     */
    public function findAll(): array;

    /**
     * Find comments by criteria.
     *
     * @param array      $criteria Search criteria
     * @param array|null $orderBy  Order by criteria
     * @param int|null   $limit    Limit
     * @param int|null   $offset   Offset
     *
     * @return array Array of comments
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * Save comment.
     *
     * @param Comment $comment Comment to save
     * @param bool    $flush   Whether to flush changes
     */
    public function save(Comment $comment, bool $flush = false): void;

    /**
     * Remove comment.
     *
     * @param Comment $comment Comment to remove
     * @param bool    $flush   Whether to flush changes
     */
    public function remove(Comment $comment, bool $flush = false): void;
}

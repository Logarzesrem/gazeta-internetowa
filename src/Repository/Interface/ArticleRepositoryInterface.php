<?php

/**
 * ArticleRepositoryInterface.
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
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

/**
 * Interface for Article repository operations.
 */
interface ArticleRepositoryInterface extends ServiceEntityRepositoryInterface
{
    /**
     * Find all articles.
     *
     * @return Article[]
     */
    public function findAll(): array;

    /**
     * Find article by ID.
     *
     * @param int|string                       $id          The article ID
     * @param \Doctrine\DBAL\LockMode|int|null $lockMode    The lock mode
     * @param int|null                         $lockVersion The lock version
     *
     * @return Article|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null);

    /**
     * Find articles with pagination and sorting.
     *
     * @param int    $page          Page number (1-based)
     * @param int    $limit         Number of items per page
     * @param string $sortField     Field to sort by
     * @param string $sortDirection Sort direction ('asc' or 'desc')
     *
     * @return array{items: Article[], total: int}
     */
    public function findPaginated(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array;

    /**
     * Find articles by category with pagination.
     *
     * @param Category $category The category to filter by
     * @param int      $page     Page number (1-based)
     * @param int      $limit    Number of items per page
     *
     * @return array{items: Article[], total: int}
     */
    public function findByCategoryPaginated(Category $category, int $page = 1, int $limit = 10): array;

    /**
     * Save article.
     *
     * @param Article $article The article to save
     * @param bool    $flush   Whether to flush the entity manager
     */
    public function save(Article $article, bool $flush = false): void;

    /**
     * Remove article.
     *
     * @param Article $article The article to remove
     * @param bool    $flush   Whether to flush the entity manager
     */
    public function remove(Article $article, bool $flush = false): void;

    /**
     * Find articles by category and status.
     *
     * @param int    $categoryId Category ID
     * @param string $status     Article status
     *
     * @return Article[]
     */
    public function findByCategoryAndStatus(int $categoryId, string $status): array;
}

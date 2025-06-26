<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Article;
use App\Entity\Category;

/**
 * Interface for Article repository operations.
 */
interface ArticleRepositoryInterface
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
     * @param \Doctrine\DBAL\LockMode|int|null $lockMode
     * @param int|null                         $lockVersion
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
     * @param int $page  Page number (1-based)
     * @param int $limit Number of items per page
     *
     * @return array{items: Article[], total: int}
     */
    public function findByCategoryPaginated(Category $category, int $page = 1, int $limit = 10): array;

    /**
     * Save article.
     */
    public function save(Article $article, bool $flush = false): void;

    /**
     * Remove article.
     */
    public function remove(Article $article, bool $flush = false): void;
}

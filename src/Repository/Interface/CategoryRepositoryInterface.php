<?php

/**
 * CategoryRepositoryInterface.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

/**
 * Interface CategoryRepositoryInterface.
 */
interface CategoryRepositoryInterface extends ServiceEntityRepositoryInterface
{
    /**
     * Find category by ID.
     *
     * @param int      $id          Category ID
     * @param int|null $lockMode    Lock mode
     * @param int|null $lockVersion Lock version
     *
     * @return Category|null Category or null if not found
     */
    public function find(int $id, ?int $lockMode = null, ?int $lockVersion = null): ?Category;

    /**
     * Find category by slug.
     *
     * @param string $slug Category slug
     *
     * @return Category|null Category or null if not found
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Find all categories.
     *
     * @return array Array of categories
     */
    public function findAll(): array;

    /**
     * Find categories by criteria.
     *
     * @param array      $criteria Search criteria
     * @param array|null $orderBy  Order by criteria
     * @param int|null   $limit    Limit
     * @param int|null   $offset   Offset
     *
     * @return array Array of categories
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * Save category.
     *
     * @param Category $category Category to save
     * @param bool     $flush    Whether to flush changes
     */
    public function save(Category $category, bool $flush = false): void;

    /**
     * Remove category.
     *
     * @param Category $category Category to remove
     * @param bool     $flush    Whether to flush changes
     */
    public function remove(Category $category, bool $flush = false): void;
}

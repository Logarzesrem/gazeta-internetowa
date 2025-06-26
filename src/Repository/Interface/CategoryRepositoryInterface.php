<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\Category;

/**
 * Interface for Category repository operations.
 */
interface CategoryRepositoryInterface
{
    /**
     * Find all categories.
     *
     * @return Category[]
     */
    public function findAll(): array;

    /**
     * Find category by ID.
     *
     * @param \Doctrine\DBAL\LockMode|int|null $lockMode
     * @param int|null                         $lockVersion
     *
     * @return Category|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null);

    /**
     * Find category by slug.
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Find all categories with their article count.
     *
     * @return array<array{category: Category, articleCount: int}>
     */
    public function findAllWithArticleCount(): array;

    /**
     * Save category.
     */
    public function save(Category $category, bool $flush = false): void;

    /**
     * Remove category.
     */
    public function remove(Category $category, bool $flush = false): void;
}

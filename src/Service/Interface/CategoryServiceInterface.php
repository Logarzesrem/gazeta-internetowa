<?php

/**
 * CategoryServiceInterface.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\Category;

/**
 * Interface CategoryServiceInterface.
 */
interface CategoryServiceInterface
{
    /**
     * Create category.
     *
     * @param Category $category Category to create
     *
     * @return Category Created category
     */
    public function create(Category $category): Category;

    /**
     * Update category.
     *
     * @param Category $category Category to update
     *
     * @return Category Updated category
     */
    public function update(Category $category): Category;

    /**
     * Delete category.
     *
     * @param Category $category Category to delete
     */
    public function delete(Category $category): void;

    /**
     * Find category by ID.
     *
     * @param int $id Category ID
     *
     * @return Category|null Category or null if not found
     */
    public function findById(int $id): ?Category;

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
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void;
}

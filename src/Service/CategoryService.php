<?php

/**
 * Service for managing categories.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\Interface\CategoryServiceInterface;

/**
 * Category service.
 */
class CategoryService implements CategoryServiceInterface
{
    /**
     * Constructor.
     *
     * @param CategoryRepository $categoryRepository Category repository
     */
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    /**
     * Create category.
     *
     * @param Category $category Category to create
     *
     * @return Category Created category
     */
    public function create(Category $category): Category
    {
        $this->categoryRepository->save($category);

        return $category;
    }

    /**
     * Update category.
     *
     * @param Category $category Category to update
     *
     * @return Category Updated category
     */
    public function update(Category $category): Category
    {
        $this->categoryRepository->save($category);

        return $category;
    }

    /**
     * Delete category.
     *
     * @param Category $category Category to delete
     */
    public function delete(Category $category): void
    {
        // If category has articles, set their category to null first
        $articles = $category->getArticles();
        foreach ($articles as $article) {
            $article->setCategory(null);
            // Save each article to persist the category removal
            $this->categoryRepository->save($article);
        }

        // Now delete the category
        $this->categoryRepository->delete($category);
    }

    /**
     * Find category by ID.
     *
     * @param int $id Category ID
     *
     * @return Category|null Category or null if not found
     */
    public function findById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Find category by slug.
     *
     * @param string $slug Category slug
     *
     * @return Category|null Category or null if not found
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findOneBy(['slug' => $slug]);
    }

    /**
     * Find all categories.
     *
     * @return array Array of categories
     */
    public function findAll(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }
}

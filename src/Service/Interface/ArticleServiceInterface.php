<?php

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\Article;
use App\Entity\Category;

/**
 * Interface for Article service operations.
 */
interface ArticleServiceInterface
{
    /**
     * Create a new article.
     *
     * @throws \InvalidArgumentException If the article data is invalid
     */
    public function createArticle(Article $article): void;

    /**
     * Update an existing article.
     *
     * @throws \InvalidArgumentException If the article data is invalid
     */
    public function updateArticle(Article $article): void;

    /**
     * Delete an article.
     */
    public function deleteArticle(Article $article): void;

    /**
     * Get article by ID.
     */
    public function getArticleById($id): ?Article;

    /**
     * Get all articles with pagination.
     *
     * @return array{items: Article[], total: int}
     */
    public function getPaginatedArticles(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array;

    /**
     * Get articles by category with pagination.
     *
     * @return array{items: Article[], total: int}
     */
    public function getArticlesByCategory(Category $category, int $page = 1, int $limit = 10): array;

    /**
     * Generate slug for article title.
     */
    public function generateSlug(string $title): string;

    /**
     * Publish/unpublish article.
     */
    public function setArticlePublished(Article $article, bool $published): void;
}

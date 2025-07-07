<?php

/**
 * ArticleServiceInterface.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

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
     * @param Article $article The article to create
     *
     * @throws \InvalidArgumentException If the article data is invalid
     */
    public function createArticle(Article $article): void;

    /**
     * Update an existing article.
     *
     * @param Article $article The article to update
     *
     * @throws \InvalidArgumentException If the article data is invalid
     */
    public function updateArticle(Article $article): void;

    /**
     * Delete an article.
     *
     * @param Article $article The article to delete
     */
    public function deleteArticle(Article $article): void;

    /**
     * Get article by ID.
     *
     * @param int $id The article ID
     *
     * @return Article|null The article or null if not found
     */
    public function getArticleById($id): ?Article;

    /**
     * Get all articles with pagination.
     *
     * @param int    $page          Page number (1-based)
     * @param int    $limit         Number of items per page
     * @param string $sortField     Field to sort by
     * @param string $sortDirection Sort direction ('asc' or 'desc')
     *
     * @return array{items: Article[], total: int}
     */
    public function getPaginatedArticles(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array;

    /**
     * Get articles by category with pagination.
     *
     * @param Category $category The category
     * @param int      $page     Page number (1-based)
     * @param int      $limit    Number of items per page
     *
     * @return array{items: Article[], total: int}
     */
    public function getArticlesByCategory(Category $category, int $page = 1, int $limit = 10): array;

    /**
     * Generate slug for article title.
     *
     * @param string $title The article title
     *
     * @return string The generated slug
     */
    public function generateSlug(string $title): string;

    /**
     * Publish/unpublish article.
     *
     * @param Article $article   The article
     * @param bool    $published The published status
     */
    public function setArticlePublished(Article $article, bool $published): void;

    /**
     * Find articles by author and status.
     *
     * @param int    $authorId Author ID
     * @param string $status   Article status
     *
     * @return Article[]
     */
    public function findByAuthorAndStatus(int $authorId, string $status): array;
}

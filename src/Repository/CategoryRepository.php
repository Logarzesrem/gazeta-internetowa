<?php

/**
 * CategoryRepository.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CategoryRepository.
 *
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('category');
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     */
    public function delete(Category $category): void
    {
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
    }

    /**
     * Find a category by its slug.
     *
     * @param string $slug The slug to search for
     *
     * @return Category|null The category or null if not found
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * Find all categories with their article count.
     *
     * @return array<array{category: Category, articleCount: int}> Array of categories with article counts
     */
    public function findAllWithArticleCount(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.name', 'ASC');

        $categories = $qb->getQuery()->getResult();

        // Get article counts using native SQL
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT c.id, COUNT(a.id) as article_count 
                FROM category c 
                LEFT JOIN article a ON c.id = a.category_id 
                GROUP BY c.id';

        $result = $connection->executeQuery($sql);
        $articleCounts = $result->fetchAllAssociative();

        // Create a map of category ID to article count
        $countMap = [];
        foreach ($articleCounts as $row) {
            $countMap[$row['id']] = (int) $row['article_count'];
        }

        // Attach article counts to categories
        foreach ($categories as $category) {
            $category->articleCount = $countMap[$category->getId()] ?? 0;
        }

        return $categories;
    }

    /**
     * Get article count for a specific category.
     *
     * @param Category $category Category entity
     *
     * @return int Article count
     */
    public function getArticleCount(Category $category): int
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT COUNT(a.id) as article_count 
                FROM category c 
                LEFT JOIN article a ON c.id = a.category_id 
                WHERE c.id = :categoryId';

        $result = $connection->executeQuery($sql, ['categoryId' => $category->getId()]);
        $row = $result->fetchAssociative();

        return (int) ($row['article_count'] ?? 0);
    }
}

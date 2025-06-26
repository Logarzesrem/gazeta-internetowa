<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Find a category by its slug.
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * Find all categories with their article count.
     *
     * @return array<array{category: Category, articleCount: int}>
     */
    public function findAllWithArticleCount(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.name', 'ASC');

        $categories = $qb->getQuery()->getResult();

        // Get article counts using native SQL
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT c.id, COUNT(ac.article_id) as article_count 
                FROM category c 
                LEFT JOIN article_category ac ON c.id = ac.category_id 
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
}

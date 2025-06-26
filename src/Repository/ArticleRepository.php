<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

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
    public function findPaginated(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array
    {
        $allowedFields = ['createdAt', 'title'];
        if (!in_array($sortField, $allowedFields, true)) {
            $sortField = 'createdAt';
        }
        $sortDirection = 'ASC' === strtoupper($sortDirection) ? 'ASC' : 'DESC';

        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.' . $sortField, $sortDirection);

        $query = $qb->getQuery();
        $query->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total,
        ];
    }

    /**
     * Find articles by category with pagination.
     *
     * @param int $page  Page number (1-based)
     * @param int $limit Number of items per page
     *
     * @return array{items: Article[], total: int}
     */
    public function findByCategoryPaginated(Category $category, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.categories', 'c')
            ->where('c = :category')
            ->setParameter('category', $category)
            ->orderBy('a.createdAt', 'DESC');

        $query = $qb->getQuery();
        $query->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total,
        ];
    }
}

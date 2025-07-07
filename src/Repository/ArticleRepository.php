<?php

/**
 * ArticleRepository.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ArticleRepository.
 *
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager Entity manager
     * @param ManagerRegistry        $registry      Manager registry
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
        $this->entityManager = $entityManager;
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
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->orderBy('a.'.$sortField, $sortDirection);

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
     * @param Category $category The category to filter by
     * @param int      $page     Page number (1-based)
     * @param int      $limit    Number of items per page
     *
     * @return array{items: Article[], total: int}
     */
    public function findByCategoryPaginated(Category $category, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.category', 'c')
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

    /**
     * Find an article by ID with category (handles orphaned references).
     *
     * @param int $id Article ID
     *
     * @return Article|null Article or null if not found
     */
    public function findWithCategory(int $id): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->where('a.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}

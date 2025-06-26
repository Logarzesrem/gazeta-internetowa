<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AdminUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for AdminUser entities.
 *
 * @extends ServiceEntityRepository<AdminUser>
 */
class AdminUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminUser::class);
    }

    /**
     * Gets paginated admin users with sorting.
     *
     * @param int    $page          The page number (1-based)
     * @param int    $limit         The number of items per page
     * @param string $sortField     The field to sort by
     * @param string $sortDirection The sort direction ('asc' or 'desc')
     *
     * @return array{items: AdminUser[], total: int} The paginated results
     */
    public function getPaginatedAdminUsers(
        int $page = 1,
        int $limit = 10,
        string $sortField = 'name',
        string $sortDirection = 'asc',
    ): array {
        $qb = $this->createQueryBuilder('au')
            ->orderBy('au.' . $sortField, $sortDirection);

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $firstResult = ($page - 1) * $limit;

        $qb->setFirstResult($firstResult)
           ->setMaxResults($limit);

        return [
            'items' => $qb->getQuery()->getResult(),
            'total' => $totalItems,
        ];
    }

    /**
     * Finds an admin user by email.
     *
     * @param string $email The email to search for
     *
     * @return AdminUser|null The admin user or null if not found
     */
    public function findByEmail(string $email): ?AdminUser
    {
        return $this->createQueryBuilder('au')
            ->where('au.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return AdminUser[] Returns an array of AdminUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AdminUser
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

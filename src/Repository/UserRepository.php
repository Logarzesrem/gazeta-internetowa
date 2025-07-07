<?php

/**
 * UserRepository.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
    }

    /**
     * Save a user entity.
     *
     * @param User $entity The user entity to save
     * @param bool $flush  Whether to flush the entity manager
     */
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a user entity.
     *
     * @param User $entity The user entity to remove
     * @param bool $flush  Whether to flush the entity manager
     */
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param PasswordAuthenticatedUserInterface $user              The user whose password is being upgraded
     * @param string                             $newHashedPassword The new hashed password
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->save($user, true);
    }

    /**
     * Find users with pagination and sorting.
     *
     * @param int    $page          Page number (1-based)
     * @param int    $limit         Number of items per page
     * @param string $sortField     Field to sort by
     * @param string $sortDirection Sort direction ('asc' or 'desc')
     *
     * @return array{items: User[], total: int}
     */
    public function findPaginated(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array
    {
        $allowedFields = ['createdAt', 'username', 'email', 'name', 'lastLoginAt'];
        if (!in_array($sortField, $allowedFields, true)) {
            $sortField = 'createdAt';
        }
        $sortDirection = 'ASC' === strtoupper($sortDirection) ? 'ASC' : 'DESC';

        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.'.$sortField, $sortDirection);

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
     * Find active users only.
     *
     * @return User[] Array of active User entities
     */
    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find user by email.
     *
     * @param string $email The email address
     *
     * @return User|null The user entity or null if not found
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Find user by username.
     *
     * @param string $username The username
     *
     * @return User|null The user entity or null if not found
     */
    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * Search users by name, username, or email.
     *
     * @param string $searchTerm The search term
     * @param int    $limit      Maximum number of results
     *
     * @return User[] Array of User entities
     */
    public function searchUsers(string $searchTerm, int $limit = 10): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :search')
            ->orWhere('u.username LIKE :search')
            ->orWhere('u.email LIKE :search')
            ->setParameter('search', '%'.$searchTerm.'%')
            ->andWhere('u.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get user statistics.
     *
     * @return array{total: int, active: int, recent: int} Array of user statistics
     */
    public function getUserStats(): array
    {
        $totalUsers = (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $activeUsers = (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();

        $recentUsers = (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt >= :date')
            ->setParameter('date', new \DateTime('-30 days'))
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'recent' => $recentUsers,
        ];
    }
}

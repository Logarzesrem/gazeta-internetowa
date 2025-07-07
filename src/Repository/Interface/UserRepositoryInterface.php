<?php

/**
 * UserRepositoryInterface.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

/**
 * Interface UserRepositoryInterface.
 */
interface UserRepositoryInterface extends ServiceEntityRepositoryInterface
{
    /**
     * Find user by ID.
     *
     * @param int      $id          User ID
     * @param int|null $lockMode    Lock mode
     * @param int|null $lockVersion Lock version
     *
     * @return User|null User or null if not found
     */
    public function find(int $id, ?int $lockMode = null, ?int $lockVersion = null): ?User;

    /**
     * Find user by email.
     *
     * @param string $email User email
     *
     * @return User|null User or null if not found
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by username.
     *
     * @param string $username User username
     *
     * @return User|null User or null if not found
     */
    public function findByUsername(string $username): ?User;

    /**
     * Find all users.
     *
     * @return array Array of users
     */
    public function findAll(): array;

    /**
     * Find users by criteria.
     *
     * @param array      $criteria Search criteria
     * @param array|null $orderBy  Order by criteria
     * @param int|null   $limit    Limit
     * @param int|null   $offset   Offset
     *
     * @return array Array of users
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * Find users with pagination.
     *
     * @param int    $page          Page number
     * @param int    $limit         Items per page
     * @param string $sortField     Sort field
     * @param string $sortDirection Sort direction
     *
     * @return array Array of users
     */
    public function findPaginated(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array;

    /**
     * Search users.
     *
     * @param string $searchTerm Search term
     * @param int    $limit      Limit
     *
     * @return array Array of users
     */
    public function searchUsers(string $searchTerm, int $limit = 10): array;

    /**
     * Save user.
     *
     * @param User $user  User to save
     * @param bool $flush Whether to flush changes
     */
    public function save(User $user, bool $flush = false): void;

    /**
     * Remove user.
     *
     * @param User $user  User to remove
     * @param bool $flush Whether to flush changes
     */
    public function remove(User $user, bool $flush = false): void;

    /**
     * Find users by role and status.
     *
     * @param string $role   User role
     * @param string $status User status
     *
     * @return User[]
     */
    public function findByRoleAndStatus(string $role, string $status): array;
}

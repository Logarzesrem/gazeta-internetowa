<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Entity\User;

/**
 * Interface for User repository operations.
 */
interface UserRepositoryInterface
{
    /**
     * Find all users.
     *
     * @return User[]
     */
    public function findAll(): array;

    /**
     * Find user by ID.
     *
     * @param \Doctrine\DBAL\LockMode|int|null $lockMode
     * @param int|null                         $lockVersion
     *
     * @return User|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null);

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by username.
     */
    public function findByUsername(string $username): ?User;

    /**
     * Find active users only.
     *
     * @return User[]
     */
    public function findActiveUsers(): array;

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
    public function findPaginated(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array;

    /**
     * Save user.
     */
    public function save(User $user, bool $flush = false): void;

    /**
     * Remove user.
     */
    public function remove(User $user, bool $flush = false): void;
}

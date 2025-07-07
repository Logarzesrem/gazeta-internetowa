<?php

/**
 * UserServiceInterface.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\User;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Create user.
     *
     * @param User $user User to create
     *
     * @return User Created user
     */
    public function create(User $user): User;

    /**
     * Update user.
     *
     * @param User $user User to update
     *
     * @return User Updated user
     */
    public function update(User $user): User;

    /**
     * Delete user.
     *
     * @param User $user User to delete
     */
    public function delete(User $user): void;

    /**
     * Find user by ID.
     *
     * @param int $id User ID
     *
     * @return User|null User or null if not found
     */
    public function findById(int $id): ?User;

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
     * Find users with pagination.
     *
     * @param int    $page          Page number (1-based)
     * @param int    $limit         Number of items per page
     * @param string $sortField     Field to sort by
     * @param string $sortDirection Sort direction ('asc' or 'desc')
     *
     * @return array{items: User[], total: int} Array of users with pagination info
     */
    public function findPaginated(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array;

    /**
     * Change user password.
     *
     * @param User   $user        User
     * @param string $newPassword New password
     *
     * @return User Updated user
     */
    public function changePassword(User $user, string $newPassword): User;

    /**
     * Toggle user active status.
     *
     * @param User $user   User
     * @param bool $active Active status
     *
     * @return User Updated user
     */
    public function toggleActive(User $user, bool $active): User;
}

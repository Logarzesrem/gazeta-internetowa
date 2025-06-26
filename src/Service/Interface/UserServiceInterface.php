<?php

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\User;

/**
 * Interface for User service operations.
 */
interface UserServiceInterface
{
    /**
     * Create a new user.
     *
     * @throws \InvalidArgumentException If the user data is invalid
     */
    public function createUser(User $user): void;

    /**
     * Update an existing user.
     *
     * @throws \InvalidArgumentException If the user data is invalid
     */
    public function updateUser(User $user): void;

    /**
     * Delete a user.
     */
    public function deleteUser(User $user): void;

    /**
     * Get user by ID.
     */
    public function getUserById($id): ?User;

    /**
     * Get user by email.
     */
    public function getUserByEmail(string $email): ?User;

    /**
     * Get user by username.
     */
    public function getUserByUsername(string $username): ?User;

    /**
     * Get all users with pagination.
     *
     * @return array{items: User[], total: int}
     */
    public function getPaginatedUsers(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array;

    /**
     * Change user password.
     */
    public function changePassword(User $user, string $newPassword): void;

    /**
     * Activate/deactivate user.
     */
    public function setUserActive(User $user, bool $active): void;
}

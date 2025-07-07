<?php

/**
 * AuthenticationServiceInterface.
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
 * Interface for Authentication service operations.
 */
interface AuthenticationServiceInterface
{
    /**
     * Authenticate user with email and password.
     *
     * @param string $email    User email
     * @param string $password User password
     *
     * @return bool True if authentication successful
     */
    public function authenticate(string $email, string $password): bool;

    /**
     * Verify user credentials.
     *
     * @param string $email    User email
     * @param string $password User password
     *
     * @return bool True if credentials are valid
     */
    public function verifyCredentials(string $email, string $password): bool;

    /**
     * Hash user password.
     *
     * @param User   $user          User
     * @param string $plainPassword Plain password
     *
     * @return string Hashed password
     */
    public function hashPassword(User $user, string $plainPassword): string;

    /**
     * Verify user password.
     *
     * @param User   $user          User
     * @param string $plainPassword Plain password
     *
     * @return bool True if password is valid
     */
    public function verifyPassword(User $user, string $plainPassword): bool;

    /**
     * Get current user.
     *
     * @param User $user User
     *
     * @return User|null Current user or null
     */
    public function getCurrentUser(User $user): ?User;

    /**
     * Logout user.
     *
     * @param User $user User to logout
     */
    public function logout(User $user): void;

    /**
     * Update user's last login timestamp.
     *
     * @param User $user The user
     */
    public function updateLastLogin(User $user): void;

    /**
     * Check if user account is active.
     *
     * @param User $user The user
     *
     * @return bool True if user is active
     */
    public function isUserActive(User $user): bool;
}

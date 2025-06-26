<?php

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Interface for Authentication service operations.
 */
interface AuthenticationServiceInterface
{
    /**
     * Authenticate user with email and password.
     *
     * @throws AuthenticationException If authentication fails
     */
    public function authenticate(string $email, string $password): User;

    /**
     * Verify if user credentials are valid.
     */
    public function verifyCredentials(string $email, string $password): bool;

    /**
     * Hash password for user.
     */
    public function hashPassword(User $user, string $plainPassword): string;

    /**
     * Check if password is valid for user.
     */
    public function isPasswordValid(User $user, string $plainPassword): bool;

    /**
     * Update user's last login timestamp.
     */
    public function updateLastLogin(User $user): void;

    /**
     * Check if user account is active.
     */
    public function isUserActive(User $user): bool;
}

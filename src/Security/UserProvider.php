<?php

/**
 * UserProvider.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Security;

use App\Entity\AdminUser;
use App\Entity\User;
use App\Repository\AdminUserRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Custom user provider that can load both regular users and admin users.
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @param UserRepository      $userRepository      The user repository service
     * @param AdminUserRepository $adminUserRepository The admin user repository service
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly AdminUserRepository $adminUserRepository)
    {
    }

    /**
     * Load a user by their identifier (email).
     *
     * @param string $identifier The user identifier (email)
     *
     * @throws UserNotFoundException When user is not found
     *
     * @return UserInterface The user interface
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // First try to find a regular user
        $user = $this->userRepository->findOneBy(['email' => $identifier]);

        if ($user) {
            return $user;
        }

        // If no regular user found, try to find an admin user
        $adminUser = $this->adminUserRepository->findOneBy(['email' => $identifier]);

        if ($adminUser) {
            return $adminUser;
        }

        throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
    }

    /**
     * Refresh a user.
     *
     * @param UserInterface $user The user to refresh
     *
     * @return UserInterface The refreshed user
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * Check if this provider supports the given user class.
     *
     * @param string $class The user class to check
     *
     * @return bool Whether this provider supports the class
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || AdminUser::class === $class;
    }
}

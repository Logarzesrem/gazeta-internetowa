<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\AdminUser;
use App\Entity\User;
use App\Repository\AdminUserRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AdminUserRepository $adminUserRepository,
    ) {
    }

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

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || AdminUser::class === $class;
    }
}

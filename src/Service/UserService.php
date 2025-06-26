<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Interface\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Service for managing users.
 */
class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function createUser(User $user): void
    {
        if (!$user->getPlainPassword()) {
            throw new \InvalidArgumentException('Plain password is required for new users');
        }

        // Check if username or email already exists
        if ($this->userRepository->findByUsername($user->getUsername())) {
            throw new \InvalidArgumentException('Username already exists');
        }

        if ($this->userRepository->findByEmail($user->getEmail())) {
            throw new \InvalidArgumentException('Email already exists');
        }

        $this->hashPassword($user);
        $this->userRepository->save($user, true);
    }

    public function updateUser(User $user): void
    {
        if ($user->getPlainPassword()) {
            $this->hashPassword($user);
        }

        // Check if username or email already exists (excluding current user)
        $existingUser = $this->userRepository->findByUsername($user->getUsername());
        if ($existingUser && $existingUser->getId() !== $user->getId()) {
            throw new \InvalidArgumentException('Username already exists');
        }

        $existingUser = $this->userRepository->findByEmail($user->getEmail());
        if ($existingUser && $existingUser->getId() !== $user->getId()) {
            throw new \InvalidArgumentException('Email already exists');
        }

        $this->userRepository->save($user, true);
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->remove($user, true);
    }

    public function getUserById($id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->userRepository->findByUsername($username);
    }

    public function getPaginatedUsers(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array
    {
        return $this->userRepository->findPaginated($page, $limit, $sortField, $sortDirection);
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user, true);
    }

    public function setUserActive(User $user, bool $active): void
    {
        $user->setIsActive($active);
        $this->userRepository->save($user, true);
    }

    /**
     * Hash the plain password of a user.
     */
    private function hashPassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();
        if (!$plainPassword) {
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }
}

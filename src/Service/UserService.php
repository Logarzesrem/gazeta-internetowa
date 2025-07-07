<?php

/**
 * UserService.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Interface\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Service for managing users.
 */
class UserService implements UserServiceInterface
{
    /**
     * @param UserRepository              $userRepository The user repository service
     * @param UserPasswordHasherInterface $passwordHasher The password hasher service
     * @param TranslatorInterface         $translator     The translator service
     * @param EntityManagerInterface      $entityManager  The entity manager
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly UserPasswordHasherInterface $passwordHasher, private readonly TranslatorInterface $translator, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Create a new user.
     *
     * @param User $user The user to create
     *
     * @throws \InvalidArgumentException When username or email already exists
     *
     * @return User Created user
     */
    public function create(User $user): User
    {
        if (!$user->getPlainPassword()) {
            throw new \InvalidArgumentException('Plain password is required for new users');
        }

        // Check if username or email already exists
        if ($user->getUsername() && $this->userRepository->findByUsername($user->getUsername())) {
            throw new \InvalidArgumentException($this->translator->trans('user.username.already_exists'));
        }

        if ($user->getEmail() && $this->userRepository->findByEmail($user->getEmail())) {
            throw new \InvalidArgumentException('Email already exists');
        }

        $this->hashPassword($user);
        $this->userRepository->save($user, true);

        return $user;
    }

    /**
     * Create a new user.
     *
     * @param User $user The user to create
     *
     * @throws \InvalidArgumentException When username or email already exists
     */
    public function createUser(User $user): void
    {
        $this->create($user);
    }

    /**
     * Update an existing user.
     *
     * @param User $user The user to update
     *
     * @throws \InvalidArgumentException When username or email already exists
     *
     * @return User Updated user
     */
    public function update(User $user): User
    {
        if ($user->getPlainPassword()) {
            $this->hashPassword($user);
        }

        // Check if username or email already exists (excluding current user)
        if ($user->getUsername()) {
            $existingUser = $this->userRepository->findByUsername($user->getUsername());
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                throw new \InvalidArgumentException($this->translator->trans('user.username.already_exists'));
            }
        }

        if ($user->getEmail()) {
            $existingUser = $this->userRepository->findByEmail($user->getEmail());
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                throw new \InvalidArgumentException('Email already exists');
            }
        }

        $this->userRepository->save($user, true);

        return $user;
    }

    /**
     * Update an existing user.
     *
     * @param User $user The user to update
     *
     * @throws \InvalidArgumentException When username or email already exists
     */
    public function updateUser(User $user): void
    {
        $this->update($user);
    }

    /**
     * Delete a user.
     *
     * @param User $user The user to delete
     */
    public function delete(User $user): void
    {
        $this->userRepository->remove($user, true);
    }

    /**
     * Delete a user.
     *
     * @param User $user The user to delete
     */
    public function deleteUser(User $user): void
    {
        $this->delete($user);
    }

    /**
     * Find user by ID.
     *
     * @param int $id User ID
     *
     * @return User|null User or null if not found
     */
    public function findById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * Get a user by ID.
     *
     * @param int $id The user ID
     *
     * @return User|null The user or null if not found
     */
    public function getUserById(int $id): ?User
    {
        return $this->findById($id);
    }

    /**
     * Find user by email.
     *
     * @param string $email User email
     *
     * @return User|null User or null if not found
     */
    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get a user by email.
     *
     * @param string $email The user email
     *
     * @return User|null The user or null if not found
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->findByEmail($email);
    }

    /**
     * Find user by username.
     *
     * @param string $username User username
     *
     * @return User|null User or null if not found
     */
    public function findByUsername(string $username): ?User
    {
        return $this->userRepository->findByUsername($username);
    }

    /**
     * Get a user by username.
     *
     * @param string $username The user username
     *
     * @return User|null The user or null if not found
     */
    public function getUserByUsername(string $username): ?User
    {
        return $this->findByUsername($username);
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
        return $this->userRepository->findPaginated($page, $limit, $sortField, $sortDirection);
    }

    /**
     * Get paginated users.
     *
     * @param int    $page          Page number (1-based)
     * @param int    $limit         Number of items per page
     * @param string $sortField     Field to sort by
     * @param string $sortDirection Sort direction ('asc' or 'desc')
     *
     * @return array{items: User[], total: int}
     */
    public function getPaginatedUsers(int $page = 1, int $limit = 10, string $sortField = 'createdAt', string $sortDirection = 'DESC'): array
    {
        return $this->findPaginated($page, $limit, $sortField, $sortDirection);
    }

    /**
     * Change a user's password.
     *
     * @param User   $user        The user
     * @param string $newPassword The new password
     *
     * @return User The updated user
     */
    public function changePassword(User $user, string $newPassword): User
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user, true);

        return $user;
    }

    /**
     * Toggle a user's active status.
     *
     * @param User $user   The user
     * @param bool $active The active status
     *
     * @return User The updated user
     */
    public function toggleActive(User $user, bool $active): User
    {
        $user->setIsActive($active);
        $this->userRepository->save($user, true);

        return $user;
    }

    /**
     * Set a user's active status.
     *
     * @param User $user   The user
     * @param bool $active The active status
     */
    public function setUserActive(User $user, bool $active): void
    {
        $this->toggleActive($user, $active);
    }

    /**
     * Hash the user's plain password.
     *
     * @param User $user The user
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

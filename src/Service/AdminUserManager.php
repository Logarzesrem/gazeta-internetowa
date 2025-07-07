<?php

/**
 * AdminUserManager.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

namespace App\Service;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Service for managing admin users.
 */
class AdminUserManager
{
    /**
     * Constructor.
     *
     * @param AdminUserRepository         $adminUserRepository Admin user repository
     * @param EntityManagerInterface      $entityManager       Entity manager
     * @param UserPasswordHasherInterface $passwordHasher      Password hasher
     */
    public function __construct(AdminUserRepository $adminUserRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->adminUserRepository = $adminUserRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Creates a new admin user.
     *
     * @param AdminUser $adminUser The admin user to create
     *
     * @throws \InvalidArgumentException If the admin user is invalid
     */
    public function createAdminUser(AdminUser $adminUser): void
    {
        if (!$adminUser->getPlainPassword()) {
            throw new \InvalidArgumentException('Plain password is required for new admin users');
        }

        $this->hashPassword($adminUser);
        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();
    }

    /**
     * Updates an existing admin user.
     *
     * @param AdminUser $adminUser The admin user to update
     *
     * @throws \InvalidArgumentException If the admin user is invalid
     */
    public function updateAdminUser(AdminUser $adminUser): void
    {
        if ($adminUser->getPlainPassword()) {
            $this->hashPassword($adminUser);
        }

        $this->entityManager->flush();
    }

    /**
     * Deletes an admin user.
     *
     * @param AdminUser $adminUser The admin user to delete
     */
    public function deleteAdminUser(AdminUser $adminUser): void
    {
        $this->entityManager->remove($adminUser);
        $this->entityManager->flush();
    }

    /**
     * Gets all admin users with pagination.
     *
     * @param int    $page          Page number
     * @param int    $limit         Number of items per page
     * @param string $sortField     Field to sort by
     * @param string $sortDirection Sort direction (asc/desc)
     *
     * @return array{items: AdminUser[], total: int}
     */
    public function getPaginatedAdminUsers(int $page = 1, int $limit = 10, string $sortField = 'name', string $sortDirection = 'asc'): array
    {
        return $this->adminUserRepository->getPaginatedAdminUsers($page, $limit, $sortField, $sortDirection);
    }

    /**
     * Get admin user by ID.
     *
     * @param int $id Admin user ID
     *
     * @return AdminUser|null Admin user or null if not found
     */
    public function getAdminUserById(int $id): ?AdminUser
    {
        return $this->adminUserRepository->find($id);
    }

    /**
     * Some other method.
     *
     * @param string $arg1 First argument
     * @param string $arg2 Second argument
     * @param string $arg3 Third argument
     */
    public function someOtherMethod(string $arg1, string $arg2, string $arg3): void
    {
        // ...
    }

    /**
     * Hashes the plain password of an admin user.
     *
     * @param AdminUser $adminUser The admin user whose password to hash
     *
     * @throws UnsupportedUserException
     */
    private function hashPassword(AdminUser $adminUser): void
    {
        $plainPassword = $adminUser->getPlainPassword();
        if (!$plainPassword) {
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, $plainPassword);
        $adminUser->setPassword($hashedPassword);
        $adminUser->eraseCredentials();
    }
}

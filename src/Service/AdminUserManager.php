<?php

declare(strict_types=1);

/**
 * AdminUserManager.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 * @copyright 2024 Konrad Stomski
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
     * @param EntityManagerInterface      $entityManager       The entity manager
     * @param AdminUserRepository         $adminUserRepository The admin user repository
     * @param UserPasswordHasherInterface $passwordHasher      The password hasher
     */
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly AdminUserRepository $adminUserRepository, private readonly UserPasswordHasherInterface $passwordHasher)
    {
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
     * @param int    $page          The page number (1-based)
     * @param int    $limit         The number of items per page
     * @param string $sortField     The field to sort by
     * @param string $sortDirection The sort direction ('asc' or 'desc')
     *
     * @return array{items: AdminUser[], total: int} The paginated results
     */
    public function getPaginatedAdminUsers(int $page = 1, int $limit = 10, string $sortField = 'name', string $sortDirection = 'asc'): array
    {
        return $this->adminUserRepository->getPaginatedAdminUsers($page, $limit, $sortField, $sortDirection);
    }

    /**
     * Gets an admin user by ID.
     *
     * @param int $id The admin user ID
     *
     * @return AdminUser|null The admin user or null if not found
     */
    public function getAdminUserById(int $id): ?AdminUser
    {
        return $this->adminUserRepository->find($id);
    }

    /**
     * Hashes the plain password of an admin user.
     *
     * @param AdminUser $adminUser The admin user
     *
     * @throws UnsupportedUserException If the user is not supported by the password hasher
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

<?php

/*
 * This file is part of the Gazeta Internetowa project.
 *
 * (c) 2025 Konrad Stomski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Admin user manager tests.
 */

namespace App\Tests\Service;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use App\Service\AdminUserManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test class for AdminUserManager.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class AdminUserManagerTest extends TestCase
{
    private AdminUserManager $adminUserManager;
    private AdminUserRepository $adminUserRepository;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->adminUserRepository = $this->createMock(AdminUserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->adminUserManager = new AdminUserManager(
            $this->adminUserRepository,
            $this->entityManager,
            $this->passwordHasher
        );
    }

    /**
     * Test admin user creation.
     */
    public function testCreateAdminUser(): void
    {
        // given
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPlainPassword('password123');

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($adminUser, 'password123')
            ->willReturn('hashed_password');

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($adminUser);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // when
        $this->adminUserManager->createAdminUser($adminUser);

        // then
        $this->assertEquals('hashed_password', $adminUser->getPassword());
    }

    /**
     * Test admin user creation without plain password.
     */
    public function testCreateAdminUserWithoutPlainPassword(): void
    {
        // given
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');

        // when & then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Plain password is required for new admin users');

        $this->adminUserManager->createAdminUser($adminUser);
    }

    /**
     * Test admin user update with password change.
     */
    public function testUpdateAdminUserWithPasswordChange(): void
    {
        // given
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setPlainPassword('newpassword123');

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($adminUser, 'newpassword123')
            ->willReturn('new_hashed_password');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // when
        $this->adminUserManager->updateAdminUser($adminUser);

        // then
        $this->assertEquals('new_hashed_password', $adminUser->getPassword());
    }

    /**
     * Test admin user update without password change.
     */
    public function testUpdateAdminUserWithoutPasswordChange(): void
    {
        // given
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // when
        $this->adminUserManager->updateAdminUser($adminUser);

        // then
        // No password hashing should occur
    }

    /**
     * Test admin user deletion.
     */
    public function testDeleteAdminUser(): void
    {
        // given
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($adminUser);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // when
        $this->adminUserManager->deleteAdminUser($adminUser);

        // then
        // Method should complete without exception
    }

    /**
     * Test getting paginated admin users.
     */
    public function testGetPaginatedAdminUsers(): void
    {
        // given
        $expectedResult = [
            'items' => [new AdminUser()],
            'total' => 1,
        ];

        $this->adminUserRepository
            ->expects($this->once())
            ->method('getPaginatedAdminUsers')
            ->with(1, 10, 'name', 'asc')
            ->willReturn($expectedResult);

        // when
        $result = $this->adminUserManager->getPaginatedAdminUsers();

        // then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test getting admin user by ID.
     */
    public function testGetAdminUserById(): void
    {
        // given
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');

        $this->adminUserRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($adminUser);

        // when
        $result = $this->adminUserManager->getAdminUserById(1);

        // then
        $this->assertSame($adminUser, $result);
    }

    /**
     * Test getting admin user by ID when user does not exist.
     */
    public function testGetAdminUserByIdWhenUserDoesNotExist(): void
    {
        // given
        $this->adminUserRepository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        // when
        $result = $this->adminUserManager->getAdminUserById(999);

        // then
        $this->assertNull($result);
    }
}

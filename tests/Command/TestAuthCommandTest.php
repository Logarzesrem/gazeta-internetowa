<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\TestAuthCommand;
use App\Entity\AdminUser;
use App\Entity\User;
use App\Repository\AdminUserRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test class for TestAuthCommand.
 *
 * Tests the functionality of the TestAuthCommand class.
 */
class TestAuthCommandTest extends TestCase
{
    private UserRepository $userRepository;
    private AdminUserRepository $adminUserRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private TestAuthCommand $command;
    private CommandTester $commandTester;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->adminUserRepository = $this->createMock(AdminUserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->command = new TestAuthCommand(
            $this->userRepository,
            $this->adminUserRepository,
            $this->passwordHasher
        );

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test command execution with users and admin users in the system.
     */
    public function testExecuteWithUsersAndAdmins(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setUsername('testuser');
        $user->setName('Test User');
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);

        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setRoles(['ROLE_ADMIN']);

        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$user]);

        $this->adminUserRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$adminUser]);

        // Mock password validation for different passwords
        $this->passwordHasher
            ->expects($this->exactly(8)) // 4 for user + 4 for admin
            ->method('isPasswordValid')
            ->willReturnMap([
                [$user, 'password123', true],
                [$user, 'test123', false],
                [$user, 'admin123', false],
                [$user, 'user123', false],
                [$adminUser, 'password123', false],
                [$adminUser, 'test123', false],
                [$adminUser, 'admin123', true],
                [$adminUser, 'user123', false],
            ]);

        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(0, $exitCode);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Testing Regular Users', $output);
        $this->assertStringContainsString('Testing Admin Users', $output);
        $this->assertStringContainsString('user@example.com', $output);
        $this->assertStringContainsString('admin@example.com', $output);
        $this->assertStringContainsString('Password \'password123\': ✅ Valid', $output);
        $this->assertStringContainsString('Password \'admin123\': ✅ Valid', $output);
        $this->assertStringContainsString('Is AdminUser instance: ✅ Yes', $output);
        $this->assertStringContainsString('Has ROLE_ADMIN: ✅ Yes', $output);
        $this->assertStringContainsString('Would redirect to: Admin Dashboard', $output);
    }

    /**
     * Test command execution with no users in the system.
     */
    public function testExecuteWithNoUsers(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->adminUserRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(0, $exitCode);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('No regular users found in database', $output);
        $this->assertStringContainsString('No admin users found in database', $output);
    }
}

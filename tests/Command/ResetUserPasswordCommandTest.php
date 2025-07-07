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

use App\Command\ResetUserPasswordCommand;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test class for ResetUserPasswordCommand.
 *
 * Tests the functionality of the ResetUserPasswordCommand class.
 */
class ResetUserPasswordCommandTest extends TestCase
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private ResetUserPasswordCommand $command;
    private CommandTester $commandTester;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new ResetUserPasswordCommand(
            $this->userRepository,
            $this->passwordHasher,
            $this->entityManager
        );

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test command execution with users in the system.
     */
    public function testExecuteWithUsers(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser');

        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$user]);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'password123')
            ->willReturn('hashed_password');

        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, 'password123')
            ->willReturn(true);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(0, $exitCode);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Password reset for user: test@example.com', $output);
        $this->assertStringContainsString('New password: password123', $output);
        $this->assertStringContainsString('Password verification: ✅ Valid', $output);
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

        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(1, $exitCode);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('No users found in database', $output);
    }
}

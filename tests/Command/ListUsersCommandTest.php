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

use App\Command\ListUsersCommand;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test class for ListUsersCommand.
 *
 * Tests the functionality of the ListUsersCommand class.
 */
class ListUsersCommandTest extends TestCase
{
    private UserRepository $userRepository;
    private ListUsersCommand $command;
    private CommandTester $commandTester;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->command = new ListUsersCommand($this->userRepository);

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test command execution with users in the system.
     */
    public function testExecuteWithUsers(): void
    {
        // Create test users
        $user1 = new User();
        $user1->setUsername('testuser1');
        $user1->setEmail('test1@example.com');
        $user1->setName('Test User 1');
        $user1->setIsActive(true);

        $user2 = new User();
        $user2->setUsername('testuser2');
        $user2->setEmail('test2@example.com');
        $user2->setName('Test User 2');
        $user2->setIsActive(false);

        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$user1, $user2]);

        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(0, $exitCode);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Users in the system:', $output);
        $this->assertStringContainsString('testuser1', $output);
        $this->assertStringContainsString('testuser2', $output);
        $this->assertStringContainsString('test1@example.com', $output);
        $this->assertStringContainsString('test2@example.com', $output);
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

        $this->assertEquals(0, $exitCode);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('No users found in the system', $output);
    }
}

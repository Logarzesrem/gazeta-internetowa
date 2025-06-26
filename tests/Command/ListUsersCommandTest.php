<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ListUsersCommand;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ListUsersCommandTest extends TestCase
{
    private UserRepository $userRepository;
    private ListUsersCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->command = new ListUsersCommand($this->userRepository);

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

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

        $this->assertStringContainsString('Registered Users', $output);
        $this->assertStringContainsString('testuser1', $output);
        $this->assertStringContainsString('testuser2', $output);
        $this->assertStringContainsString('test1@example.com', $output);
        $this->assertStringContainsString('test2@example.com', $output);
        $this->assertStringContainsString('Found 2 user(s)', $output);
    }

    public function testExecuteWithNoUsers(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(0, $exitCode);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('No users found in the database', $output);
    }
}

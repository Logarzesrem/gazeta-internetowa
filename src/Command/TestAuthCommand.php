<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AdminUserRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:test-auth',
    description: 'Test user and admin authentication',
)]
class TestAuthCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AdminUserRepository $adminUserRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Test regular users
        $users = $this->userRepository->findAll();
        if (!empty($users)) {
            $io->section('Testing Regular Users');
            $user = $users[0];
            $io->info("Testing authentication for user: {$user->getEmail()}");

            // Test password verification
            $testPasswords = ['password123', 'test123', 'admin123', 'user123'];

            foreach ($testPasswords as $testPassword) {
                $isValid = $this->passwordHasher->isPasswordValid($user, $testPassword);
                $io->text("Password '{$testPassword}': " . ($isValid ? '✅ Valid' : '❌ Invalid'));
            }

            // Show user details
            $io->section('User Details');
            $io->table(
                ['Property', 'Value'],
                [
                    ['ID', $user->getId()],
                    ['Email', $user->getEmail()],
                    ['Username', $user->getUsername()],
                    ['Name', $user->getName()],
                    ['Roles', implode(', ', $user->getRoles())],
                    ['Active', $user->isActive() ? 'Yes' : 'No'],
                    ['Created', $user->getCreatedAt()->format('Y-m-d H:i:s')],
                ]
            );
        } else {
            $io->warning('No regular users found in database.');
        }

        // Test admin users
        $adminUsers = $this->adminUserRepository->findAll();
        if (!empty($adminUsers)) {
            $io->section('Testing Admin Users');
            $adminUser = $adminUsers[0];
            $io->info("Testing authentication for admin: {$adminUser->getEmail()}");

            // Test password verification
            $testPasswords = ['password123', 'test123', 'admin123', 'user123'];

            foreach ($testPasswords as $testPassword) {
                $isValid = $this->passwordHasher->isPasswordValid($adminUser, $testPassword);
                $io->text("Password '{$testPassword}': " . ($isValid ? '✅ Valid' : '❌ Invalid'));
            }

            // Show admin details
            $io->section('Admin User Details');
            $io->table(
                ['Property', 'Value'],
                [
                    ['ID', $adminUser->getId()],
                    ['Email', $adminUser->getEmail()],
                    ['Name', $adminUser->getName()],
                    ['Roles', implode(', ', $adminUser->getRoles())],
                ]
            );

            // Test role-based redirect logic
            $io->section('Role-Based Redirect Test');
            $hasAdminRole = in_array('ROLE_ADMIN', $adminUser->getRoles());
            $isAdminUser = $adminUser instanceof \App\Entity\AdminUser;
            $io->text('Is AdminUser instance: ' . ($isAdminUser ? '✅ Yes' : '❌ No'));
            $io->text('Has ROLE_ADMIN: ' . ($hasAdminRole ? '✅ Yes' : '❌ No'));
            $io->text('Would redirect to: ' . ($hasAdminRole || $isAdminUser ? 'Admin Dashboard' : 'Articles Page'));
        } else {
            $io->warning('No admin users found in database.');
        }

        return Command::SUCCESS;
    }
}

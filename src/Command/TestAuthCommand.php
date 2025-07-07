<?php

/**
 * TestAuthCommand.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

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

/**
 * Command to test user and admin authentication functionality.
 */
#[AsCommand(name: 'app:test-auth', description: 'Test user and admin authentication')]
class TestAuthCommand extends Command
{
    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository      User repository
     * @param AdminUserRepository         $adminUserRepository Admin user repository
     * @param UserPasswordHasherInterface $passwordHasher      Password hasher
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly AdminUserRepository $adminUserRepository, private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    /**
     * Execute the command to test authentication.
     *
     * @param InputInterface  $input  The input interface
     * @param OutputInterface $output The output interface
     *
     * @return int The command exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->userRepository->findAll();
        if (!empty($users)) {
            $io->section('Testing Regular Users');
            $user = $users[0];
            $io->info('Testing authentication for user: '.$user->getEmail());
            $testPasswords = ['password123', 'test123', 'admin123', 'user123'];
            foreach ($testPasswords as $testPassword) {
                $isValid = $this->passwordHasher->isPasswordValid($user, $testPassword);
                $io->text('Password \''.$testPassword.'\': '.($isValid ? '✅ Valid' : '❌ Invalid'));
            }
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
                    ['Created', $user->getCreatedAt() ? $user->getCreatedAt()->format('Y-m-d H:i:s') : ''],
                ]
            );
        } else {
            $io->warning('No regular users found in database.');
        }
        $adminUsers = $this->adminUserRepository->findAll();
        if (!empty($adminUsers)) {
            $io->section('Testing Admin Users');
            $adminUser = $adminUsers[0];
            $io->info('Testing authentication for admin: '.$adminUser->getEmail());
            $testPasswords = ['password123', 'test123', 'admin123', 'user123'];
            foreach ($testPasswords as $testPassword) {
                $isValid = $this->passwordHasher->isPasswordValid($adminUser, $testPassword);
                $io->text('Password \''.$testPassword.'\': '.($isValid ? '✅ Valid' : '❌ Invalid'));
            }
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
            $io->section('Role-Based Redirect Test');
            $hasAdminRole = in_array('ROLE_ADMIN', $adminUser->getRoles());
            $io->text('Is AdminUser instance: ✅ Yes');
            $io->text('Has ROLE_ADMIN: '.($hasAdminRole ? '✅ Yes' : '❌ No'));
            $io->text('Would redirect to: '.($hasAdminRole ? 'Admin Dashboard' : 'Articles Page'));
        } else {
            $io->warning('No admin users found in database.');
        }

        return Command::SUCCESS;
    }
}

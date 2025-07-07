<?php

/*
 * This file is part of the Gazeta Internetowa project.
 *
 * (c) 2024 Gazeta Internetowa
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Command to reset user password.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
#[AsCommand(name: 'app:reset-user-password', description: 'Reset user password')]
class ResetUserPasswordCommand extends Command
{
    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param EntityManagerInterface      $entityManager  Entity manager
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly UserPasswordHasherInterface $passwordHasher, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    /**
     * Execute the command to reset a user's password.
     *
     * @param InputInterface  $input  Input interface
     * @param OutputInterface $output Output interface
     *
     * @return int Command exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->userRepository->findAll();
        if (empty($users)) {
            $io->error('No users found in database.');

            return Command::FAILURE;
        }
        $user = $users[0];
        $newPassword = 'password123';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();
        $io->success('Password reset for user: '.$user->getEmail());
        $io->info("New password: {$newPassword}");
        $isValid = $this->passwordHasher->isPasswordValid($user, $newPassword);
        $io->text('Password verification: '.($isValid ? '✅ Valid' : '❌ Invalid'));

        return Command::SUCCESS;
    }
}

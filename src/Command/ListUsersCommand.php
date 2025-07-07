<?php

/*
 * This file is part of the Gazeta Internetowa project.
 *
 * (c) 2024 Gazeta Internetowa
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ListUsersCommand.
 *
 * Command to list all users in the system.
 */
#[AsCommand(
    name: 'app:list-users',
    description: 'List all users in the system',
)]
class ListUsersCommand extends Command
{
    /**
     * Constructor.
     *
     * @param UserRepository $userRepository User repository
     */
    public function __construct(private UserRepository $userRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the command.
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
            $io->warning('No users found in the system.');

            return Command::SUCCESS;
        }

        $io->title('Users in the system:');
        $io->table(
            ['ID', 'Username', 'Email', 'Name', 'Status', 'Created'],
            array_map(function ($user) {
                return [
                    $user->getId(),
                    $user->getUsername(),
                    $user->getEmail(),
                    $user->getName(),
                    $user->isActive() ? 'Active' : 'Inactive',
                    $user->getCreatedAt() ? $user->getCreatedAt()->format('Y-m-d H:i:s') : '',
                ];
            }, $users)
        );

        return Command::SUCCESS;
    }
}

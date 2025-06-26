<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list-users',
    description: 'List all registered users',
)]
class ListUsersCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findAll();

        if (empty($users)) {
            $io->warning('No users found in the database.');

            return Command::SUCCESS;
        }

        $io->title('Registered Users');

        $tableData = [];
        foreach ($users as $user) {
            $tableData[] = [
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getName(),
                $user->getCreatedAt()->format('Y-m-d H:i:s'),
                $user->isActive() ? 'Yes' : 'No',
            ];
        }

        $io->table(
            ['ID', 'Username', 'Email', 'Name', 'Created At', 'Active'],
            $tableData
        );

        $io->success(sprintf('Found %d user(s)', count($users)));

        return Command::SUCCESS;
    }
}

<?php

/**
 * FixOrphanedCategoriesCommand.
 *
 * @author   Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license  MIT
 */

declare(strict_types=1);

namespace App\Command;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class FixOrphanedCategoriesCommand.
 */
#[AsCommand(name: 'app:fix-orphaned-categories', description: 'Fix orphaned category references in articles')]
class FixOrphanedCategoriesCommand extends Command
{
    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager Entity manager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
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
        $io->title('Fixing orphaned category references');
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $fixedCount = 0;
        foreach ($articles as $article) {
            $category = $article->getCategory();
            if (null !== $category) {
                $categoryExists = $this->entityManager
                    ->getRepository(\App\Entity\Category::class)
                    ->find($category->getId());
                if (null === $categoryExists) {
                    $message = sprintf(
                        'Fixing article "%s" (ID: %d) - removing orphaned category reference',
                        $article->getTitle(),
                        $article->getId()
                    );
                    $io->text($message);
                    $article->setCategory(null);
                    ++$fixedCount;
                }
            }
        }
        if ($fixedCount > 0) {
            $this->entityManager->flush();
            $io->success(
                sprintf('Fixed %d articles with orphaned category references', $fixedCount)
            );
        } else {
            $io->info('No orphaned category references found');
        }

        return Command::SUCCESS;
    }
}

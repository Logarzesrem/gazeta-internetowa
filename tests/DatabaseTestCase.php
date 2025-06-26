<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class DatabaseTestCase extends WebTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Clean up database before each test
        $this->cleanDatabase();
    }

    protected function cleanDatabase(): void
    {
        $connection = $this->entityManager->getConnection();

        // Disable foreign key checks temporarily
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

        // Get all table names
        $tables = $connection->createSchemaManager()->listTableNames();

        // Truncate all tables
        foreach ($tables as $table) {
            $connection->executeStatement("TRUNCATE TABLE `{$table}`");
        }

        // Re-enable foreign key checks
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');

        // Clear entity manager
        $this->entityManager->clear();
    }
}

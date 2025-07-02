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

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Abstract base class for database tests.
 *
 * This class provides database cleanup functionality for tests.
 */
abstract class AbstractDatabaseTestCase extends WebTestCase
{
    protected EntityManagerInterface $entityManager;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Clean up database before each test
        $this->cleanDatabase();
    }

    /**
     * Clean the database by truncating all tables.
     */
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

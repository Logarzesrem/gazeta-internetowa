<?php

/**
 * Abstract base test case.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract base test case for all tests.
 */
abstract class AbstractBaseTestCase extends TestCase
{
    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    protected ManagerRegistry $managerRegistry;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
    }

    /**
     * Create a mock repository.
     *
     * @param string $entityClass Entity class name
     *
     * @return ServiceEntityRepository Mock repository
     */
    protected function createMockRepository(string $entityClass): ServiceEntityRepository
    {
        return $this->createMock(ServiceEntityRepository::class);
    }
}

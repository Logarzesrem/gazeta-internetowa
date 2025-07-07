<?php

/**
 * AdminUserFixture.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fixture for loading admin users.
 */
class AdminUserFixture extends Fixture
{
    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher The password hasher service
     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load admin user fixtures.
     *
     * @param ObjectManager $manager The object manager
     */
    public function load(ObjectManager $manager): void
    {
        // Clear existing admin users
        $manager->getRepository(AdminUser::class)->createQueryBuilder('a')->delete()->getQuery()->execute();

        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setName('Admin User');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin123'));
        $adminUser->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminUser);

        $manager->flush();
    }
}

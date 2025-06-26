<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Clear existing comments first (they reference users)
        $manager->createQuery('DELETE FROM App\Entity\Comment')->execute();

        // Clear existing users
        $manager->createQuery('DELETE FROM App\Entity\User')->execute();

        $user1 = new User();
        $user1->setEmail('user@example.com');
        $user1->setUsername('user1');
        $user1->setName('User1');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password123'));
        $user1->setRoles(['ROLE_USER']);
        $user1->setIsActive(true);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setUsername('user2');
        $user2->setName('User2');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password123'));
        $user2->setRoles(['ROLE_USER']);
        $user2->setIsActive(true);
        $manager->persist($user2);

        $manager->flush();
    }
}

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

namespace App\Tests\DataFixtures;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Tests\AbstractDatabaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test class for UserFixture.
 *
 * Tests the functionality of the UserFixture class.
 */
class UserFixtureTest extends AbstractDatabaseTestCase
{
    private UserFixture $fixture;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->passwordHasher = $this->getContainer()->get(UserPasswordHasherInterface::class);
        $this->fixture = new UserFixture($this->passwordHasher);
    }

    /**
     * Test loading the fixture.
     */
    public function testLoad(): void
    {
        $manager = $this->getContainer()->get(EntityManagerInterface::class);

        // Load the fixture
        $this->fixture->load($manager);

        // Check that users were created
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->findAll();

        $this->assertCount(2, $users);

        // Check first user
        $user1 = $userRepository->findOneBy(['email' => 'user@example.com']);
        $this->assertNotNull($user1);
        $this->assertEquals('user1', $user1->getUsername());
        $this->assertEquals('User1', $user1->getName());
        $this->assertEquals(['ROLE_USER'], $user1->getRoles());
        $this->assertTrue($user1->isActive());

        // Check second user
        $user2 = $userRepository->findOneBy(['email' => 'user2@example.com']);
        $this->assertNotNull($user2);
        $this->assertEquals('user2', $user2->getUsername());
        $this->assertEquals('User2', $user2->getName());
        $this->assertEquals(['ROLE_USER'], $user2->getRoles());
        $this->assertTrue($user2->isActive());

        // Verify passwords are hashed
        $this->assertTrue($this->passwordHasher->isPasswordValid($user1, 'password123'));
        $this->assertTrue($this->passwordHasher->isPasswordValid($user2, 'password123'));
    }

    /**
     * Test that loading the fixture clears existing users.
     */
    public function testLoadClearsExistingUsers(): void
    {
        $manager = $this->getContainer()->get(EntityManagerInterface::class);

        // Create an existing user first with properly hashed password
        $existingUser = new User();
        $existingUser->setEmail('existing@example.com');
        $existingUser->setUsername('existing');
        $existingUser->setName('Existing User');
        $existingUser->setPassword($this->passwordHasher->hashPassword($existingUser, 'password'));
        $manager->persist($existingUser);
        $manager->flush();

        // Verify user exists
        $userRepository = $manager->getRepository(User::class);
        $this->assertNotNull($userRepository->findOneBy(['email' => 'existing@example.com']));

        // Load the fixture
        $this->fixture->load($manager);

        // Check that only the fixture users exist
        $users = $userRepository->findAll();
        $this->assertCount(2, $users);

        // Verify the existing user was removed
        $this->assertNull($userRepository->findOneBy(['email' => 'existing@example.com']));
    }
}

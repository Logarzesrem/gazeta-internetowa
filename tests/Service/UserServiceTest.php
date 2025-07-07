<?php

/*
 * This file is part of the Gazeta Internetowa project.
 *
 * (c) 2025 Konrad Stomski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * User service tests.
 */

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Tests\DatabaseTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test class for UserService.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class UserServiceTest extends DatabaseTestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->getContainer()->get('translator')->setLocale('pl');

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->passwordHasher = $this->getContainer()->get(UserPasswordHasherInterface::class);

        $this->userService = new UserService(
            $this->userRepository,
            $this->passwordHasher,
            $this->getContainer()->get(\Symfony\Contracts\Translation\TranslatorInterface::class),
            $this->entityManager
        );
    }

    /**
     * Test user creation.
     */
    public function testCreateUser(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');

        $this->userService->createUser($user);

        $this->assertNotNull($user->getId());
        $this->assertNotEquals('password123', $user->getPassword());
        $this->assertNull($user->getPlainPassword()); // Should be erased
    }

    /**
     * Test user creation without plain password.
     */
    public function testCreateUserWithoutPlainPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Plain password is required for new users');

        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');

        $this->userService->createUser($user);
    }

    /**
     * Test user creation with existing username.
     */
    public function testCreateUserWithExistingUsername(): void
    {
        // Create first user
        $user1 = new User();
        $user1->setUsername('testuser');
        $user1->setEmail('test1@example.com');
        $user1->setName('Test User 1');
        $user1->setPlainPassword('password123');
        $this->userService->createUser($user1);

        // Try to create second user with same username
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nazwa użytkownika już istnieje.');

        $user2 = new User();
        $user2->setUsername('testuser');
        $user2->setEmail('test2@example.com');
        $user2->setName('Test User 2');
        $user2->setPlainPassword('password123');

        $this->userService->createUser($user2);
    }

    /**
     * Test user creation with existing email.
     */
    public function testCreateUserWithExistingEmail(): void
    {
        // Create first user
        $user1 = new User();
        $user1->setUsername('testuser1');
        $user1->setEmail('test@example.com');
        $user1->setName('Test User 1');
        $user1->setPlainPassword('password123');
        $this->userService->createUser($user1);

        // Try to create second user with same email
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email already exists');

        $user2 = new User();
        $user2->setUsername('testuser2');
        $user2->setEmail('test@example.com');
        $user2->setName('Test User 2');
        $user2->setPlainPassword('password123');

        $this->userService->createUser($user2);
    }

    /**
     * Test user update.
     */
    public function testUpdateUser(): void
    {
        // Create user
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        // Update user
        $user->setUsername('updateduser');
        $user->setEmail('updated@example.com');
        $user->setName('Updated User');
        $user->setPlainPassword('newpassword123');

        $this->userService->updateUser($user);

        $updatedUser = $this->userService->getUserById($user->getId());
        $this->assertEquals('updateduser', $updatedUser->getUsername());
        $this->assertEquals('updated@example.com', $updatedUser->getEmail());
        $this->assertNotEquals('newpassword123', $updatedUser->getPassword());
    }

    /**
     * Test user update without password.
     */
    public function testUpdateUserWithoutPassword(): void
    {
        // Create user
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        // Update user without changing password
        $user->setUsername('updateduser');
        $user->setEmail('updated@example.com');
        $user->setName('Updated User');

        $this->userService->updateUser($user);

        $updatedUser = $this->userService->getUserById($user->getId());
        $this->assertEquals('updateduser', $updatedUser->getUsername());
        $this->assertEquals('updated@example.com', $updatedUser->getEmail());
    }

    /**
     * Test user update with existing username.
     */
    public function testUpdateUserWithExistingUsername(): void
    {
        // Create first user
        $user1 = new User();
        $user1->setUsername('testuser1');
        $user1->setEmail('test1@example.com');
        $user1->setName('Test User 1');
        $user1->setPlainPassword('password123');
        $this->userService->createUser($user1);

        // Create second user
        $user2 = new User();
        $user2->setUsername('testuser2');
        $user2->setEmail('test2@example.com');
        $user2->setName('Test User 2');
        $user2->setPlainPassword('password123');
        $this->userService->createUser($user2);

        // Try to update second user with first user's username
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nazwa użytkownika już istnieje.');

        $user2->setUsername('testuser1');
        $this->userService->updateUser($user2);
    }

    /**
     * Test user update with existing email.
     */
    public function testUpdateUserWithExistingEmail(): void
    {
        // Create first user
        $user1 = new User();
        $user1->setUsername('testuser1');
        $user1->setEmail('test@example.com');
        $user1->setName('Test User 1');
        $user1->setPlainPassword('password123');
        $this->userService->createUser($user1);

        // Create second user
        $user2 = new User();
        $user2->setUsername('testuser2');
        $user2->setEmail('test2@example.com');
        $user2->setName('Test User 2');
        $user2->setPlainPassword('password123');
        $this->userService->createUser($user2);

        // Try to update second user with first user's email
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email already exists');

        $user2->setEmail('test@example.com');
        $this->userService->updateUser($user2);
    }

    /**
     * Test user deletion.
     */
    public function testDeleteUser(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        $userId = $user->getId();
        $this->userService->deleteUser($user);

        $this->assertNull($this->userService->getUserById($userId));
    }

    /**
     * Test get user by ID.
     */
    public function testGetUserById(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        $foundUser = $this->userService->getUserById($user->getId());
        $this->assertEquals($user->getId(), $foundUser->getId());
    }

    /**
     * Test get user by ID not found.
     */
    public function testGetUserByIdNotFound(): void
    {
        $this->assertNull($this->userService->getUserById(999));
    }

    /**
     * Test get user by email.
     */
    public function testGetUserByEmail(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        $foundUser = $this->userService->getUserByEmail('test@example.com');
        $this->assertEquals($user->getId(), $foundUser->getId());
    }

    /**
     * Test get user by email not found.
     */
    public function testGetUserByEmailNotFound(): void
    {
        $this->assertNull($this->userService->getUserByEmail('nonexistent@example.com'));
    }

    /**
     * Test get user by username.
     */
    public function testGetUserByUsername(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        $foundUser = $this->userService->getUserByUsername('testuser');
        $this->assertEquals($user->getId(), $foundUser->getId());
    }

    /**
     * Test get user by username not found.
     */
    public function testGetUserByUsernameNotFound(): void
    {
        $this->assertNull($this->userService->getUserByUsername('nonexistent'));
    }

    /**
     * Test get paginated users.
     */
    public function testGetPaginatedUsers(): void
    {
        // Create multiple users
        for ($i = 1; $i <= 15; ++$i) {
            $user = new User();
            $user->setUsername("user{$i}");
            $user->setEmail("user{$i}@example.com");
            $user->setName("User {$i}");
            $user->setPlainPassword('password123');
            $this->userService->createUser($user);
        }

        $result = $this->userService->getPaginatedUsers(1, 10);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(10, $result['items']);
        $this->assertEquals(15, $result['total']);

        $result2 = $this->userService->getPaginatedUsers(2, 10);
        $this->assertCount(5, $result2['items']);
        $this->assertEquals(15, $result2['total']);
    }

    /**
     * Test change password.
     */
    public function testChangePassword(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        $oldPassword = $user->getPassword();
        $this->userService->changePassword($user, 'newpassword123');

        $updatedUser = $this->userService->getUserById($user->getId());
        $this->assertNotEquals($oldPassword, $updatedUser->getPassword());
    }

    /**
     * Test set user active.
     */
    public function testSetUserActive(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPlainPassword('password123');
        $this->userService->createUser($user);

        $this->userService->setUserActive($user, false);
        $updatedUser = $this->userService->getUserById($user->getId());
        $this->assertFalse($updatedUser->isActive());

        $this->userService->setUserActive($user, true);
        $updatedUser = $this->userService->getUserById($user->getId());
        $this->assertTrue($updatedUser->isActive());
    }
}

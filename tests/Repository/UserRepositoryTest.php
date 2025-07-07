<?php

/*
 * This file is part of the Gazeta Internetowa project.
 *
 * (c) 2025 Konrad Stomski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\DatabaseTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Test class for UserRepository.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */
class UserRepositoryTest extends DatabaseTestCase
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->passwordHasher = $this->getContainer()->get(UserPasswordHasherInterface::class);
    }

    /**
     * Test save method.
     */
    public function testSave(): void
    {
        $user = $this->createUserWithHashedPassword('testuser_save', 'test_save@example.com', 'MySecureP@ssw0rd!');

        // Persist the user first
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Now test the save method (which should just persist and flush)
        $this->userRepository->save($user, true);

        $this->assertNotNull($user->getId());
        $this->assertNotNull($this->userRepository->find($user->getId()));
    }

    /**
     * Test remove method.
     */
    public function testRemove(): void
    {
        $user = $this->createUserWithHashedPassword('testuser_remove', 'test_remove@example.com', 'Complex#Pass123');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();
        $this->userRepository->remove($user, true);

        $this->assertNull($this->userRepository->find($userId));
    }

    /**
     * Test upgrade password method.
     */
    public function testUpgradePassword(): void
    {
        $user = $this->createUserWithHashedPassword('testuser_upgrade', 'test_upgrade@example.com', 'InitialP@ss!');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $newHashedPassword = 'new_hashed_password';
        $this->userRepository->upgradePassword($user, $newHashedPassword);

        $updatedUser = $this->userRepository->find($user->getId());
        $this->assertEquals($newHashedPassword, $updatedUser->getPassword());
    }

    /**
     * Test upgrade password with unsupported user.
     */
    public function testUpgradePasswordWithUnsupportedUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $unsupportedUser = $this->createMock(PasswordAuthenticatedUserInterface::class);
        $this->userRepository->upgradePassword($unsupportedUser, 'new_password');
    }

    /**
     * Test find paginated method.
     */
    public function testFindPaginated(): void
    {
        // Create test users with diverse passwords
        $passwords = [
            'SimplePass123',
            'Complex@Pass#456',
            'VeryLongPasswordWithSpecialChars!@#$%^&*()',
            'MixedCase123!@#',
            'NumbersOnly123456789',
            'SymbolsOnly!@#$%^&*()',
            'ShortP@ss',
            'VeryLongPasswordThatExceedsNormalLength123!@#$%^&*()',
            'UnicodePassword🎉🚀💻',
            'Spaces In Password 123',
            'Tab\tSeparated\tPassword',
            'NewLine\nPassword\n123',
            'Quotes"Password"123',
            'Backslash\\Password\\123',
            'NullByte\0Password',
        ];

        for ($i = 1; $i <= 15; ++$i) {
            $user = $this->createUserWithHashedPassword("user{$i}", "user{$i}@example.com", $passwords[$i - 1]);
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        $result = $this->userRepository->findPaginated(1, 10);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(10, $result['items']);
        $this->assertEquals(15, $result['total']);

        $result2 = $this->userRepository->findPaginated(2, 10);
        $this->assertCount(5, $result2['items']);
        $this->assertEquals(15, $result2['total']);
    }

    /**
     * Test find paginated with sorting.
     */
    public function testFindPaginatedWithSorting(): void
    {
        $user1 = $this->createUserWithHashedPassword('zebra_user_sort', 'zebra_sort@example.com', 'ZebraP@ss!');
        $this->entityManager->persist($user1);

        $user2 = $this->createUserWithHashedPassword('alpha_user_sort', 'alpha_sort@example.com', 'AlphaP@ss!');
        $this->entityManager->persist($user2);

        $this->entityManager->flush();

        // Test sorting by username ASC
        $result = $this->userRepository->findPaginated(1, 10, 'username', 'ASC');
        $this->assertEquals('alpha_user_sort', $result['items'][0]->getUsername());
        $this->assertEquals('zebra_user_sort', $result['items'][1]->getUsername());

        // Test sorting by username DESC
        $result = $this->userRepository->findPaginated(1, 10, 'username', 'DESC');
        $this->assertEquals('zebra_user_sort', $result['items'][0]->getUsername());
        $this->assertEquals('alpha_user_sort', $result['items'][1]->getUsername());
    }

    /**
     * Test find paginated with invalid sort field.
     */
    public function testFindPaginatedWithInvalidSortField(): void
    {
        $user = $this->createUserWithHashedPassword('testuser_invalid', 'test_invalid@example.com', 'TestP@ss!');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Test with invalid sort field (should default to createdAt)
        $result = $this->userRepository->findPaginated(1, 10, 'invalid_field', 'ASC');

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(1, $result['items']);
        $this->assertEquals(1, $result['total']);
    }

    /**
     * Test find active users method.
     */
    public function testFindActiveUsers(): void
    {
        $activeUser1 = $this->createUserWithHashedPassword('active1_test', 'active1_test@example.com', 'Active1P@ss!');
        $activeUser1->setIsActive(true);
        $this->entityManager->persist($activeUser1);

        $activeUser2 = $this->createUserWithHashedPassword('active2_test', 'active2_test@example.com', 'Active2P@ss!');
        $activeUser2->setIsActive(true);
        $this->entityManager->persist($activeUser2);

        $inactiveUser = $this->createUserWithHashedPassword('inactive_test', 'inactive_test@example.com', 'InactiveP@ss!');
        $inactiveUser->setIsActive(false);
        $this->entityManager->persist($inactiveUser);

        $this->entityManager->flush();

        $activeUsers = $this->userRepository->findActiveUsers();

        $this->assertCount(2, $activeUsers);
        foreach ($activeUsers as $user) {
            $this->assertTrue($user->isActive());
        }
    }

    /**
     * Test find by email method.
     */
    public function testFindByEmail(): void
    {
        $user = $this->createUserWithHashedPassword('testuser_email', 'test_email@example.com', 'EmailTestP@ss!');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $foundUser = $this->userRepository->findByEmail('test_email@example.com');
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->getId(), $foundUser->getId());
    }

    /**
     * Test find by email not found.
     */
    public function testFindByEmailNotFound(): void
    {
        $foundUser = $this->userRepository->findByEmail('nonexistent@example.com');
        $this->assertNull($foundUser);
    }

    /**
     * Test find by username method.
     */
    public function testFindByUsername(): void
    {
        $user = $this->createUserWithHashedPassword('testuser_username', 'test_username@example.com', 'UsernameTestP@ss!');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $foundUser = $this->userRepository->findByUsername('testuser_username');
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->getId(), $foundUser->getId());
    }

    /**
     * Test find by username not found.
     */
    public function testFindByUsernameNotFound(): void
    {
        $foundUser = $this->userRepository->findByUsername('nonexistent');
        $this->assertNull($foundUser);
    }

    /**
     * Test search users method.
     */
    public function testSearchUsers(): void
    {
        $user1 = $this->createUserWithHashedPassword('john_doe_search', 'john_search@example.com', 'JohnP@ss!');
        $user1->setIsActive(true);
        $this->entityManager->persist($user1);

        $user2 = $this->createUserWithHashedPassword('jane_smith_search', 'jane_search@example.com', 'JaneP@ss!');
        $user2->setIsActive(true);
        $this->entityManager->persist($user2);

        $inactiveUser = $this->createUserWithHashedPassword('inactive_john_search', 'inactive_search@example.com', 'InactiveJohnP@ss!');
        $inactiveUser->setIsActive(false);
        $this->entityManager->persist($inactiveUser);

        $this->entityManager->flush();

        // Search by username
        $results = $this->userRepository->searchUsers('john');
        $this->assertCount(1, $results);
        $this->assertEquals('john_doe_search', $results[0]->getUsername());

        // Search by email
        $results = $this->userRepository->searchUsers('jane_search@example.com');
        $this->assertCount(1, $results);
        $this->assertEquals('jane_smith_search', $results[0]->getUsername());

        // Search with limit
        $results = $this->userRepository->searchUsers('j', 1);
        $this->assertCount(1, $results);
    }

    /**
     * Test get user stats method.
     */
    public function testGetUserStats(): void
    {
        // Create users with different creation dates and diverse passwords
        $oldUser = $this->createUserWithHashedPassword('olduser_stats', 'old_stats@example.com', 'OldUserP@ss!');
        $oldUser->setIsActive(true);
        $this->entityManager->persist($oldUser);

        $recentUser = $this->createUserWithHashedPassword('recentuser_stats', 'recent_stats@example.com', 'RecentUserP@ss!');
        $recentUser->setIsActive(true);
        $this->entityManager->persist($recentUser);

        $inactiveUser = $this->createUserWithHashedPassword('inactiveuser_stats', 'inactive_stats@example.com', 'InactiveUserP@ss!');
        $inactiveUser->setIsActive(false);
        $this->entityManager->persist($inactiveUser);

        $this->entityManager->flush();

        $stats = $this->userRepository->getUserStats();

        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('recent', $stats);
        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(2, $stats['active']);
        $this->assertGreaterThanOrEqual(2, $stats['recent']); // At least 2 recent users
    }

    /**
     * Test password with special characters.
     */
    public function testPasswordWithSpecialCharacters(): void
    {
        $specialPasswords = [
            'P@ssw0rd!',
            'MyP@ss#123',
            'Complex$Pass%456',
            'Very^Long&Pass*789',
            'Pass(with)chars',
            'Pass[with]brackets',
            'Pass{with}braces',
            'Pass<with>angles',
            'Pass\'with\'quotes',
            'Pass"with"quotes',
            'Pass\\with\\backslashes',
            'Pass/with/slashes',
            'Pass|with|pipes',
            'Pass~with~tildes',
            'Pass`with`backticks',
        ];

        foreach ($specialPasswords as $index => $password) {
            $user = $this->createUserWithHashedPassword("specialuser{$index}", "special{$index}@example.com", $password);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        // Verify all users were created successfully
        $users = $this->userRepository->findAll();
        $this->assertGreaterThanOrEqual(count($specialPasswords), count($users));
    }

    /**
     * Test password with unicode characters.
     */
    public function testPasswordWithUnicodeCharacters(): void
    {
        $unicodePasswords = [
            'P@ssw0rd🎉',
            'MyP@ss🚀123',
            'Complex💻Pass456',
            'Very🔥LongPass789',
            'Pass🌟with🌟stars',
            'Pass🎭with🎭masks',
            'Pass🎪with🎪circus',
            'Pass🎨with🎨art',
            'Pass🎯with🎯target',
            'Pass🎲with🎲dice',
        ];

        foreach ($unicodePasswords as $index => $password) {
            $user = $this->createUserWithHashedPassword("unicodeuser{$index}", "unicode{$index}@example.com", $password);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        // Verify all users were created successfully
        $users = $this->userRepository->findAll();
        $this->assertGreaterThanOrEqual(count($unicodePasswords), count($users));
    }

    /**
     * Create user with hashed password.
     *
     * @param string      $username      The username
     * @param string      $email         The email
     * @param string      $plainPassword The plain password
     * @param string|null $name          The name
     *
     * @return User The created user
     */
    private function createUserWithHashedPassword(string $username, string $email, string $plainPassword, ?string $name = null): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($plainPassword);
        $user->setName($name ?? $username); // Use username as name if not provided

        // Hash the password manually
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        return $user;
    }
}

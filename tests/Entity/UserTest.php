<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

/**
 * User entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    private User $user;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->user = new User();
    }

    /**
     * Test user setters and getters.
     */
    public function testUserSettersAndGetters(): void
    {
        // given
        $email = 'test@example.com';
        $name = 'Test User';
        $username = 'testuser';

        // when
        $this->user->setEmail($email);
        $this->user->setName($name);
        $this->user->setUsername($username);

        // then
        $this->assertEquals($email, $this->user->getEmail());
        $this->assertEquals($name, $this->user->getName());
        $this->assertEquals($username, $this->user->getUsername());
    }

    /**
     * Test user roles.
     */
    public function testUserRoles(): void
    {
        // given
        $roles = ['ROLE_USER'];

        // when
        $this->user->setRoles($roles);

        // then
        $this->assertEquals($roles, $this->user->getRoles());
    }

    /**
     * Test user password.
     */
    public function testUserPassword(): void
    {
        // given
        $password = 'hashed_password';

        // when
        $this->user->setPassword($password);

        // then
        $this->assertEquals($password, $this->user->getPassword());
    }
}

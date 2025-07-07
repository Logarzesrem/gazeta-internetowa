<?php

/**
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Admin controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\AdminUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AdminControllerTest.
 */
class AdminControllerTest extends WebTestCase
{
    /**
     * Test admin dashboard route without authentication.
     */
    public function testAdminDashboardWithoutAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/admin');
        $this->assertResponseRedirects();
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Test admin dashboard route with authentication.
     */
    public function testAdminDashboardWithAuth(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);
        $client->loginUser($adminUser);

        $client->request('GET', '/en/admin');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test admin dashboard content.
     */
    public function testAdminDashboardContent(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);
        $client->loginUser($adminUser);

        $crawler = $client->request('GET', '/en/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    /**
     * Test admin users list route.
     */
    public function testAdminUsersListRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/admin/users');
        $this->assertResponseRedirects();
    }

    /**
     * Test admin user creation route without authentication.
     */
    public function testAdminUserCreationWithoutAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/admin/users/new');
        $this->assertResponseRedirects();
    }

    /**
     * Test admin user edit route without authentication.
     */
    public function testAdminUserEditWithoutAuth(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);

        $client->request('GET', '/en/admin/users/'.$adminUser->getId().'/edit');
        $this->assertResponseRedirects();
    }

    /**
     * Test admin user deletion route without authentication.
     */
    public function testAdminUserDeleteWithoutAuth(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);

        $client->request('POST', '/en/admin/users/'.$adminUser->getId().'/delete');
        $this->assertResponseRedirects();
    }

    /**
     * Test admin user change password route without authentication.
     */
    public function testAdminUserChangePasswordWithoutAuth(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);

        $client->request('GET', '/en/admin/users/'.$adminUser->getId().'/password');
        $this->assertResponseRedirects();
    }

    /**
     * Test admin user view route without authentication.
     */
    public function testAdminUserViewWithoutAuth(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);

        $client->request('GET', '/en/admin/users/'.$adminUser->getId());
        $this->assertResponseRedirects();
    }

    /**
     * Test admin user activation route without authentication.
     */
    public function testAdminUserActivationWithoutAuth(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);

        $client->request('POST', '/en/admin/users/'.$adminUser->getId().'/activate');
        $this->assertResponseRedirects();
    }

    /**
     * Test admin user deactivation route without authentication.
     */
    public function testAdminUserDeactivationWithoutAuth(): void
    {
        $client = static::createClient();

        // Create admin user after createClient()
        $adminUser = $this->createAdminUser($client);

        $client->request('POST', '/en/admin/users/'.$adminUser->getId().'/deactivate');
        $this->assertResponseRedirects();
    }

    /**
     * Test Polish admin routes.
     */
    public function testPolishAdminRoutes(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pl/admin');
        $this->assertResponseRedirects();
    }

    /**
     * Helper method to create an admin user for testing.
     *
     * @param mixed $client The test client
     *
     * @return AdminUser The created admin user
     */
    private function createAdminUser($client): AdminUser
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $adminUser = new AdminUser();
        $adminUser->setEmail('admin_'.uniqid().'@test.com'); // Unique email per test
        $adminUser->setName('Test Admin');
        $adminUser->setPassword($passwordHasher->hashPassword($adminUser, 'password123'));
        $adminUser->setRoles(['ROLE_ADMIN']);

        $entityManager->persist($adminUser);
        $entityManager->flush();

        return $adminUser;
    }
}

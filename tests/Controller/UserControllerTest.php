<?php

/**
 * User controller tests.
 */

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest.
 */
class UserControllerTest extends WebTestCase
{
    /**
     * Test user registration route.
     */
    public function testUserRegistrationRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/en/register');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Test user login route.
     */
    public function testUserLoginRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/en/user/login');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Test user profile route without authentication.
     */
    public function testUserProfileWithoutAuth(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/en/profile');

        // then
        $this->assertResponseRedirects();
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Test user profile edit route without authentication.
     */
    public function testUserProfileEditWithoutAuth(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/en/profile/edit');

        // then
        $this->assertResponseRedirects();
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Test user change password route without authentication.
     */
    public function testUserChangePasswordWithoutAuth(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/en/profile/change-password');

        // then
        $this->assertResponseRedirects();
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Test user logout route.
     */
    public function testUserLogoutRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/en/user/logout');

        // then
        $this->assertResponseRedirects();
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Test user registration form submission.
     */
    public function testUserRegistrationFormSubmission(): void
    {
        // given
        $client = static::createClient();

        // First, get the registration form to extract the CSRF token
        $crawler = $client->request('GET', '/en/register');
        $this->assertResponseIsSuccessful();

        // Extract the CSRF token from the form
        $csrfToken = $crawler->filter('input[name="user_registration[_token]"]')->attr('value');

        // Generate unique values to avoid conflicts with existing data
        $uniqueId = uniqid();
        $uniqueEmail = "test_{$uniqueId}@example.com";
        $uniqueUsername = "testuser_{$uniqueId}";

        // when
        $client->request('POST', '/en/register', [
            'user_registration' => [
                '_token' => $csrfToken,
                'email' => $uniqueEmail,
                'name' => 'Test User',
                'username' => $uniqueUsername,
                'plainPassword' => [
                    'first' => 'password123',
                    'second' => 'password123',
                ],
            ],
        ]);

        // then
        // Registration should succeed with unique values
        $this->assertResponseRedirects();
    }
}

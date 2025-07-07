<?php

/**
 * Home controller tests.
 */

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomeControllerTest.
 */
class HomeControllerTest extends WebTestCase
{
    /**
     * Test home page route.
     */
    public function testHomePageRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/');

        // then
        // Root route serves home page with default locale (en)
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test home page content.
     */
    public function testHomePageContent(): void
    {
        // given
        $client = static::createClient();

        // when
        $crawler = $client->request('GET', '/en');

        // then
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    /**
     * Test Polish home page route.
     */
    public function testPolishHomePageRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/pl');

        // then
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test Polish home page content.
     */
    public function testPolishHomePageContent(): void
    {
        // given
        $client = static::createClient();

        // when
        $crawler = $client->request('GET', '/pl');

        // then
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }
}

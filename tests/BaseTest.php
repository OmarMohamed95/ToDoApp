<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    use FixturesTrait;

    private $client;

    /**
     * Get service container
     * 
     * @return Container
     */
    protected function getContainer()
    {
        self::bootKernel();
        return self::$kernel->getContainer();
    }

    protected function getAuthenticatedClient()
    {
        $route = $this->getRoute('api_login_check');

        $this->client = static::createClient();
        $this->client->request(
        'POST',
        $route,
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'email' => 'test1@test.com',
            'password' => 'test1',
            ])
        );

        return $this->client;
    }

    protected function getRoute(string $routeName, array $parameters = [])
    {
        return $this->getContainer()->get('router')->generate($routeName, $parameters);
    }
}
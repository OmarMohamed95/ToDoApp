<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    private $client;

    protected function createAuthenticatedClient($email = 'test1@test.com', $password = 'test1')
    {
        $this->client = static::createClient();
        $this->client->request(
        'POST',
        'http://127.0.0.1:8000/api/login_check',
        array(),
        array(),
        array('CONTENT_TYPE' => 'application/json'),
        json_encode(array(
            'email' => $email,
            'password' => $password,
            ))
        );

        return $this->client;
    }
}
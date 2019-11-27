<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase,
    Symfony\Component\HttpFoundation\Response,

    App\Entity\Users;

class UsersControllerTest extends WebTestCase
{
    const API_URL = 'http://tests/rest';

    private $client = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetUserList()
    {
        $this->client->request('GET', self::API_URL.'/users');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testGetUserById()
    {
        $this->client->request('GET', self::API_URL.'/users/0');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testGetUserTransactions()
    {
        $this->client->request('GET', self::API_URL.'/users/0/transactions');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testMakeTransaction()
    {
        $this->client->request('POST', self::API_URL.'/users/0/transactions');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $json = $this->client->getResponse()->getContent();
        $this->assertContains('error', $json);
    }
}

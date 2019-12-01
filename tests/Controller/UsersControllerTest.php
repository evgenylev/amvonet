<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase,
    Symfony\Component\HttpFoundation\Response,

    App\Entity\Users;

class UsersControllerTest extends WebTestCase
{
    const API_URL = 'http://tests/rest';
    const TEST_USER_NAME = 'test_user';

    /**
     * @var KernelBrowser
     */
    private static $client = null;

    /**
     * @var int
     */
    private static $testUserId = false;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$client = static::createClient();

        //adding a test user
        $doctrine = static::$client->getContainer()->get('doctrine');
        $testUser = new Users();
        $testUser->setUserName(static::TEST_USER_NAME);
        $em = $doctrine->getManager();
        $em->persist($testUser);
        $em->flush();
        static::$testUserId = $testUser->getId();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public static function tearDownAfterClass()
    {
        $doctrine = static::$client->getContainer()->get('doctrine');
        $conn = $doctrine->getConnection();
        $stmt = $conn->prepare('DELETE FROM `transactions` WHERE `debet_user_id`='.static::$testUserId);
        $stmt->execute();
        $stmt = $conn->prepare('DELETE FROM `users` WHERE `id`='.static::$testUserId);
        $stmt->execute();

        parent::tearDownAfterClass();
    }

    public function testGetUserList()
    {
        //check if a test user exists
        $this->assertGreaterThan(0, static::$testUserId);

        static::$client->request('GET', static::API_URL.'/users');
        $this->assertEquals(Response::HTTP_OK, static::$client->getResponse()->getStatusCode());
        $content = static::$client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertContains(static::TEST_USER_NAME, $content);
    }

    public function testGetUserById()
    {
        //check if a test user exists
        $this->assertGreaterThan(0, static::$testUserId);

        static::$client->request('GET', self::API_URL.'/users/0');
        $this->assertJson(static::$client->getResponse()->getContent());
        $this->assertEquals(Response::HTTP_NOT_FOUND, static::$client->getResponse()->getStatusCode());

        static::$client->request('GET', static::API_URL.'/users/'.static::$testUserId);
        $content = static::$client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertEquals(Response::HTTP_OK, static::$client->getResponse()->getStatusCode());
        $user = json_decode($content, true);
        $this->assertEquals(static::TEST_USER_NAME, $user['user_name']);
        $this->assertEquals(10000, $user['balance']);
    }

    public function testGetUserTransactions()
    {
        //check if a test user exists
        $this->assertGreaterThan(0, static::$testUserId);

        static::$client->request('GET', static::API_URL.'/users/0/transactions');
        $this->assertEquals(Response::HTTP_NOT_FOUND, static::$client->getResponse()->getStatusCode());
    }

    public function testMakeTransaction()
    {
        //check if a test user exists
        $this->assertGreaterThan(0, static::$testUserId);

        //user does not exist (id=0) -> error
        static::$client->request('POST', static::API_URL.'/users/0/transactions');
        $this->assertEquals(Response::HTTP_OK, static::$client->getResponse()->getStatusCode());
        $json = static::$client->getResponse()->getContent();
        $this->assertContains('error', $json);

        static::$client->request('POST', static::API_URL.'/users/'.static::$testUserId.'/transactions', ['to'=>1, 'summ'=>5000]);
        $answer = json_decode(static::$client->getResponse()->getContent(), true);

        $this->assertArrayNotHasKey('error', $answer);
        $this->assertArrayHasKey('state', $answer);
        $this->assertEquals($answer['state'], 'ok');

        //$this->testGetUserByIdForTestUser();
    }

    public function testGetUserByIdForTestUser()
    {
        //check if a test user exists
        $this->assertGreaterThan(0, static::$testUserId);

        static::$client->request('GET', static::API_URL.'/users/'.static::$testUserId);
        $content = static::$client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertEquals(Response::HTTP_OK, static::$client->getResponse()->getStatusCode());
        $user = json_decode($content, true);
        $this->assertEquals(static::TEST_USER_NAME, $user['user_name']);
        $this->assertEquals(5000, $user['balance']);
    }
}

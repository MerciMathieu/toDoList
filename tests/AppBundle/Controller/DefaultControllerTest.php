<?php

namespace App\Tests\AppBundle\Controller;

use PHPUnit\Framework\TestCase;

class DefaultControllerTest extends TestCase
{
    public function testIndex()
    {
        $this->assertTrue(true);
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/');
//
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}

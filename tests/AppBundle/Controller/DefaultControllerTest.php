<?php

namespace App\Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepageRedirectToLoginForAnonymous(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertRouteSame('login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testHomepageIsUp(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $client->submitForm('Se connecter', ['_username' => 'test', '_password' => 'test']);

        $client->followRedirect();

        $this->assertRouteSame('homepage');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

//        $this->assertSame(4, $crawler->filter('.row')->count());
//        $this->assertSame(5, $crawler->filter('html:contains("a")')->count());

    }
}

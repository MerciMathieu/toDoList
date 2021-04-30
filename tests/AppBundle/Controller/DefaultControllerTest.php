<?php

namespace App\Tests\AppBundle\Controller;

use App\Repository\UserRepository;
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

    public function testHomepageIsUpWhenLoggedIn(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $client->request('GET', '/');

        $this->assertRouteSame('homepage');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

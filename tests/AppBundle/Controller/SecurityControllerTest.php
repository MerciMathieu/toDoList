<?php

namespace App\Tests\AppBundle\Controller;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testLoginPageIsUp(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateUserButtonOnLoginPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        $client->clickLink("Créer un utilisateur");
        $this->assertRouteSame('user_create');
    }

    public function testSubmitButtonOnLoginPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'test',
            '_password' => 'test',
        ]);

        $this->assertStringContainsString("test", $client->getInternalRequest()->getParameters()['_username']);
        $this->assertStringContainsString("test", $client->getInternalRequest()->getParameters()['_password']);
    }

    public function testLoginAction(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'test';
        $form['_password'] = 'test';
        $client->submit($form);

        $client->followRedirect();

        $this->assertRouteSame('homepage');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginActionFail(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'test';
        $form['_password'] = 'tes';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('.alert-danger')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Invalid credentials.")')->count() > 0);
        $this->assertNotSame('/', $crawler->getUri());
    }

    public function testLogoutAction(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);
        $client->request('GET', '/');

        $client->clickLink("Se déconnecter");
        $this->assertRouteSame('logout');

        $client->followRedirect();
        $crawler = $client->followRedirect();

        $this->assertRouteSame('login');
        $this->assertTrue($crawler->filter('html:contains("Se connecter")')->count() > 0);
    }

    public function testForbiddenAccessIfNotAdmin(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);
        $client->request('GET', '/users');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testGrantedAccessIfAdmin(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('admin@test.fr');
        $client->loginUser($user);
        $client->request('GET', '/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

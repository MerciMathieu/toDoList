<?php

namespace App\Tests\AppBundle\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsUp(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->filter('html:contains("Créer un utilisateur")')->count());
        $this->assertSame(1, $crawler->filter('form')->count());

        $client->clickLink("Créer un utilisateur");
        $this->assertRouteSame('user_create');
    }

    public function testLoginAction(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Se connecter')->form();

        $form['_username'] = 'test';
        $form['_password'] = 'test';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertRouteSame('homepage');
    }

    public function testVisitingWhileLoggedIn(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');

        $client->loginUser($user);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }
}

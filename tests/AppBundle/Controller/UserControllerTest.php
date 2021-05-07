<?php

namespace App\Tests\AppBundle\Controller;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testListPageIsUp(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $client->request('GET', '/users');

        $this->assertRouteSame('user_list');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testListPageElements(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/users');

        $this->assertTrue($crawler->filter('table')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("test")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("test@test.fr")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Edit")')->count() > 0);
    }

    public function testCreateUserButton(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $client->request('GET', '/users');
        $client->clickLink("Créer un utilisateur");

        $this->assertRouteSame('user_create');
    }

    public function testCreateUserPageIsUp()
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $client->request('GET', '/users/create');

        $this->assertRouteSame('user_create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateUserAction(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton("Ajouter")->form();
        $form['user[username]'] = 'user';
        $form['user[password][first]'] = 'user';
        $form['user[password][second]'] = 'user';
        $form['user[email]'] = 'user@user.fr';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertRouteSame('user_list');
        $this->assertTrue($crawler->filter('html:contains("user")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("user@user.fr")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("L\'utilisateur a bien été ajouté.")')->count() > 0);
    }

    public function testEditUserButton(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $client->request('GET', '/users');
        $client->clickLink("Edit");

        $this->assertRouteSame('user_edit');
    }

    public function testEditUserPageIsUp()
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $userId = $user->getId();
        $client->loginUser($user);

        $client->request('GET', "/users/$userId/edit");

        $this->assertRouteSame('user_edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditUserFormData(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $userId = $user->getId();
        $client->loginUser($user);

        $crawler = $client->request('GET', "/users/$userId/edit");

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertNotEmpty($form->getValues()['user[username]']);
        $this->assertEmpty($form->getValues()['user[password][first]']);
        $this->assertEmpty($form->getValues()['user[password][second]']);
        $this->assertNotEmpty($form->getValues()['user[email]']);
    }

    public function testDataChangeAfterEdit(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $userId = $user->getId();
        $client->loginUser($user);

        $crawler = $client->request('GET', "/users/$userId/edit");

        $form = $crawler->selectButton('Modifier')->form();
        $form->setValues([
            'user[username]' => 'updated',
            'user[password][first]' => 'updated',
            'user[password][second]' => 'updated',
            'user[email]' => 'updatedemail@test.fr']);
        $client->submit($form);

        $crawler = $client->followRedirect();

        $updatedUser = $userRepository->findOneById($userId);

        $this->assertSame('updated', $updatedUser->getUsername());
        $this->assertSame('updatedemail@test.fr', $updatedUser->getEmail());
    }
}

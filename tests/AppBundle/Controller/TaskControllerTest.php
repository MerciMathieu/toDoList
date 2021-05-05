<?php

namespace App\Tests\AppBundle\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testListPageIsUp(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $client->request('GET', '/tasks');
        $this->assertRouteSame('task_list');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEmptyList(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tasks');

        $this->assertTrue($crawler->filter('html:contains("Il n\'y a pas encore de tâche enregistrée.")')->count() > 0);
    }

    public function testListCard(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tasks');

        $this->assertTrue($crawler->filter('.thumbnail')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Supprimer")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Marquer comme faite")')->count() > 0);
    }

    public function testCreateTaskForm(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'test';
        $form['task[content]'] = 'test';
        $client->submit($form);

        $client->followRedirect();

        $this->assertRouteSame('task_list');
    }

    public function testEditTaskFormGetData(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $task = $taskRepository->findOneBy(['title' => 'test']);
        $taskId = $task->getId();

        $crawler = $client->request('GET', "/tasks/$taskId/edit");

        $form = $crawler->selectButton('Modifier')->form();
        $this->assertNotEmpty($form->getValues()['task[title]']);
        $this->assertNotEmpty($form->getValues()['task[content]']);
    }

    public function testSubmitEditTaskFormG(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $task = $taskRepository->findOneBy(['title' => 'test']);
        $taskId = $task->getId();

        $crawler = $client->request('GET', "/tasks/$taskId/edit");

        $this->assertRouteSame('task_edit');

        $form = $crawler->selectButton('Modifier')->form();
        $client->submit($form);
        $client->followRedirect();

        $this->assertRouteSame('task_list');
    }

    public function testDataChangeAfterEdit(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $task = $taskRepository->findOneBy(['title' => 'test']);
        $taskId = $task->getId();

        $crawler = $client->request('GET', "/tasks/$taskId/edit");

        $form = $crawler->selectButton('Modifier')->form();

        $form->setValues([
            'task[title]' => 'updated title',
            'task[content]' => 'updated content']);

        $client->submit($form);

        $updatedTask = $taskRepository->findOneById($taskId);
        $this->assertSame('updated title', $updatedTask->getTitle());
        $this->assertSame('updated content', $updatedTask->getContent());
    }
}

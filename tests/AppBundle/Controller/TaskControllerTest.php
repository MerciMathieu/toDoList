<?php

namespace App\Tests\AppBundle\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testListPageIsUp(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

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

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');

        $client->loginUser($user);

        $this->setExcludedDoctrineTables(array('user'));
        $this->loadFixtures();

        $crawler = $client->request('GET', '/tasks');

        $this->assertTrue($crawler->filter('html:contains("Il n\'y a pas encore de tâche enregistrée.")')->count() > 0);
    }

    public function testListCard(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

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

        $this->loadFixtures(['App\DataFixtures\CreateUserTestData']);

        $taskRepository = static::$container->get(TaskRepository::class);
        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $this->setExcludedDoctrineTables(array('user'));
        $this->loadFixtures();

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'test';
        $form['task[content]'] = 'test';
        $client->submit($form);

        $client->followRedirect();

        $this->assertRouteSame('task_list');
        $this->assertNotNull($taskRepository->findOneBy(['title' => 'test']));
    }

    public function testEditTaskFormGetData(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

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

    public function testSubmitEditTaskForm(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

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
        $crawler = $client->followRedirect();

        $this->assertRouteSame('task_list');
        $this->assertTrue($crawler->filter('.alert-success')->count() > 0);
    }

    public function testDataChangeAfterEdit(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

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

    public function testToggleTaskAction(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $task = $taskRepository->findOneBy(['title' => 'test']);
        $taskId = $task->getId();

        $this->assertFalse($task->getIsDone());

        $client->request('GET', "/tasks/$taskId/toggle");

        $this->assertTrue($task->getIsDone());
    }

    public function testToggleTaskButton(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $crawler = $client->request('GET', "/tasks");

        $this->assertRouteSame('task_list');
        $this->assertTrue($crawler->filter('html:contains("Marquer comme faite")')->count() > 0);

        $form = $crawler->selectButton('Marquer comme faite')->form();
        $client->submit($form);

        $this->assertRouteSame('task_toggle');
    }

    public function testRemoveTaskAction(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $task = $taskRepository->findOneBy(['title' => 'test']);
        $taskId = $task->getId();

        $client->request('GET', "/tasks/$taskId/delete");

        $this->assertRouteSame('task_delete');
        $this->assertEmpty($taskRepository->findOneById($taskId));
    }

    public function testFlashAfterRemoveTask(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($user);

        $crawler = $client->request('GET', "/tasks");

        $form = $crawler->selectButton('Supprimer')->form();
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("La tâche a bien été supprimée.")')->count() > 0);
        $this->assertRouteSame('task_list');
    }

    public function testRemoveFailIfNotTaskAuthor(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');
        $user2 = $userRepository->findOneByEmail('test2@test.fr');

        $client->loginUser($user2);

        $task = $taskRepository->findOneBy(['author' => $user]);
        $taskId = $task->getId();

        $client->request('GET', "/tasks/$taskId/delete");

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testRemoveTaskWithAnonymousAuthorFailIfNotAdmin(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $user = $userRepository->findOneByEmail('test@test.fr');

        $client->loginUser($user);

        $task = $taskRepository->findOneBy(['author' => null]);
        $taskId = $task->getId();

        $client->request('GET', "/tasks/$taskId/delete");

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testRemoveTaskWithAnonymousAuthorSuccessIfAdmin(): void
    {
        $client = static::createClient();

        $this->loadFixtures(['App\DataFixtures\CreateTaskTestData']);
        $this->loadFixtures(['App\DataFixtures\CreateAdminUserTestData']);

        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $admin = $userRepository->findOneByEmail('admin@test.fr');

        $client->loginUser($admin);

        $task = $taskRepository->findOneBy(['author' => null]);
        $taskId = $task->getId();

        $client->request('GET', "/tasks/$taskId/delete");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

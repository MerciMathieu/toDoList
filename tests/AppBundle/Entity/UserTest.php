<?php

namespace App\Tests\AppBundle\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetRoles()
    {
        $user = new User();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testUserGetTasks()
    {
        $user = new User();
        $user->setUsername('test');
        $user->setPassword('test');
        $user->setEmail('test@test.fr');

        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');
        $task->setAuthor($user);

        $user->addTask($task);

        $this->assertEquals(1, $user->getTasks()->count());
    }

    public function testUserRemoveTask()
    {
        $user = new User();
        $user->setUsername('test');
        $user->setPassword('test');
        $user->setEmail('test@test.fr');

        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');
        $task->setAuthor($user);

        $user->addTask($task);

        $this->assertEquals(1, $user->getTasks()->count());

        $user->removeTask($task);

        $this->assertEquals(0, $user->getTasks()->count());
    }

    public function testAddUserRole()
    {
        $user = new User();
        $user->setUsername('test');
        $user->setPassword('test');
        $user->setEmail('test@test.fr');
        $user->setRole('ROLE_ADMIN');

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
    }
}

<?php

namespace App\Tests\AppBundle\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testNewTaskIsNotDone()
    {
        $task = new Task();
        $this->assertFalse($task->getIsDone());
    }

    public function testToggle()
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertTrue($task->getIsDone());
        $task->toggle(false);
        $this->assertFalse($task->getIsDone());
    }

    public function testSetIsDone()
    {
        $task = new Task();
        $task->setIsDone(true);
        $this->assertTrue($task->getIsDone());
    }

    public function testGetCreatedAt()
    {
        $task = new Task();
        $this->assertNotNull($task->getCreatedAt());
    }

    public function testSetCreatedAt()
    {
        $task = new Task();
        $task->setCreatedAt(new \DateTime());
        $this->assertNotNull($task->getCreatedAt());
    }
}

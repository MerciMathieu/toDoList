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
}

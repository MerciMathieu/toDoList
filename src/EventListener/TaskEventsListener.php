<?php

namespace App\EventListener;

use App\Entity\Task;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class TaskEventsListener
{
    public function prePersist(Task $task, LifecycleEventArgs $event): void
    {
        $task->setCreatedAt(new \Datetime());
        $task->setIsDone(false);
    }
}

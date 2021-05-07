<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class CreateTaskTestData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');

        $manager->persist($task);
        $manager->flush();
    }
}

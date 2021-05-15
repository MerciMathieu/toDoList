<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class CreateTaskTestData extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');

        $user = $this->getReference(CreateUserTestData::TEST_USER_REFERENCE);
        /** @var User $user */
        $task->setAuthor($user);
        $manager->persist($task);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CreateUserTestData::class
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @codeCoverageIgnore
 */
class CreateTaskTestData extends Fixture
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('test');
        $user->setPassword($this->encoderFactory->getEncoder(User::class)->encodePassword('test', null));
        $user->setEmail('test@test.fr');

        $manager->persist($user);

        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');

        $manager->persist($task);
        $manager->flush();
    }
}

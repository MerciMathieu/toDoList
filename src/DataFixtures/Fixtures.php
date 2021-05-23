<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @codeCoverageIgnore
 */
class Fixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getGroups(): array
    {
        return ['devGroup'];
    }

    public function load(ObjectManager $manager)
    {
        $adminUser = new User();
        $adminUser->setUsername('Admin');
        $adminUser->setEmail('admin@todo.list');
        $adminUser->setPassword($this->encoder->encodePassword($adminUser, 'admin'));
        $adminUser->setRole('ROLE_ADMIN');

        $manager->persist($adminUser);

        $regularUser = new User();
        $regularUser->setUsername('user');
        $regularUser->setEmail('user@toto.list');
        $regularUser->setPassword($this->encoder->encodePassword($regularUser, 'user'));

        $manager->persist($regularUser);

        for ($i=0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle("tâche $i");
            $task->setContent("Contenu de la tâche $i");
            $task->setAuthor(null);

            $manager->persist($task);
        }

        $manager->flush();
    }
}

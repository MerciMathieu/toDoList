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
        $adminUser->setEmail('admin@toto.list');
        $adminUser->setPassword($this->encoder->encodePassword($adminUser, 'admin'));
        $adminUser->setRoles('ROLE_ADMIN');

        $manager->persist($adminUser);

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

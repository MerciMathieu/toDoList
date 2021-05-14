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
        $anonymousUser = new User();
        $anonymousUser->setUsername('Anonymous');
        $anonymousUser->setEmail('anonymous@toto.list');
        $anonymousUser->setPassword($this->encoder->encodePassword($anonymousUser, 'anonymoususer'));
        $anonymousUser->setRoles(['IS_AUTHENTICATED_ANONYMOUSLY']);

        $adminUser = new User();
        $adminUser->setUsername('Admin');
        $adminUser->setEmail('admin@toto.list');
        $adminUser->setPassword($this->encoder->encodePassword($adminUser, 'admin'));
        $adminUser->setRoles(['ROLE_ADMIN']);

        $manager->persist($anonymousUser);
        $manager->persist($adminUser);

        for ($i=0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle("tâche $i");
            $task->setContent("Contenu de la tâche $i");
            $task->setAuthor($anonymousUser);

            $manager->persist($task);
        }

        $manager->flush();
    }
}

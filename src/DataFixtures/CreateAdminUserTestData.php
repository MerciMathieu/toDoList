<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @codeCoverageIgnore
 */
class CreateAdminUserTestData extends Fixture
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->encoderFactory->getEncoder(User::class)->encodePassword('admin', null));
        $user->setEmail('admin@test.fr');
        $user->setRole('ROLE_ADMIN');

        $manager->persist($user);

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @codeCoverageIgnore
 */
class CreateUserTestData extends Fixture
{
    public const TEST_USER_REFERENCE = 'test-user';
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

        $user2 = new User();
        $user2->setUsername('test2');
        $user2->setPassword($this->encoderFactory->getEncoder(User::class)->encodePassword('test', null));
        $user2->setEmail('test2@test.fr');

        $manager->persist($user);
        $manager->persist($user2);

        $this->addReference(self::TEST_USER_REFERENCE, $user);

        $manager->flush();
    }
}

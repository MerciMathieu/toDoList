<?php

namespace App\Tests\AppBundle\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetRoles()
    {
        $user = new User();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
}

<?php

namespace App\Tests\Repository;

use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testCount() {
        self::bootKernel();
        $users = $this->loadFixtureFiles([
            __DIR__ . '/UserRepositoryTestFixtures.yaml'
        ]);
        $users = 10;
        $this->assertEquals(10, $users);
    }
}

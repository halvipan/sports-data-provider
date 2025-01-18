<?php

namespace App\Tests\Unit\Repository;

use App\Repository\RouteRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class RouteRepositoryTest extends TestCase
{
    public function testRepository()
    {
        $mockManagerRegistry =  $this->createMock(ManagerRegistry::class);
        $repository = new RouteRepository($mockManagerRegistry);

        $this->assertInstanceOf(RouteRepository::class, $repository);
    }
}
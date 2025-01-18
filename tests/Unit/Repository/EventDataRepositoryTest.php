<?php

namespace App\Tests\Unit\Repository;

use App\Repository\EventDataRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class EventDataRepositoryTest extends TestCase
{
    public function testRepository()
    {
        $mockManagerRegistry =  $this->createMock(ManagerRegistry::class);
        $repository = new EventDataRepository($mockManagerRegistry);

        $this->assertInstanceOf(EventDataRepository::class, $repository);
    }
}
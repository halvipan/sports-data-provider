<?php

namespace App\Tests\Unit\Repository;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Mapping\ClassMetadata;

class EventRepositoryTest extends TestCase
{
    public function testRepository()
    {
        $mockManagerRegistry =  $this->createMock(ManagerRegistry::class);
        $repository = new EventRepository($mockManagerRegistry);

        $this->assertInstanceOf(EventRepository::class, $repository);
    }

    public function testFindThrowsEntityNotFoundException()
    {
        $mockManagerRegistry = $this->createMock(ManagerRegistry::class);
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockClassMetadata = $this->createMock(ClassMetadata::class);

        $mockClassMetadata->name = Event::class;

        $mockEntityManager
            ->method('getClassMetadata')
            ->willReturn($mockClassMetadata);

        $mockManagerRegistry
            ->method('getManagerForClass')
            ->willReturn($mockEntityManager);

        $mockEntityManager
            ->method('find')
            ->willReturn(null);

        $repository = new EventRepository($mockManagerRegistry);

        $this->expectException(EntityNotFoundException::class);

        $repository->find(1);
    }
}
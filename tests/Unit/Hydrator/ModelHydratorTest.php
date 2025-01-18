<?php

namespace App\Tests\Unit\Hydrator;

use App\Entity\Event;
use App\Entity\Model;
use App\Entity\Route;
use App\Hydrator\ModelHydrator;
use App\Hydrator\RouteHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class ModelHydratorTest extends TestCase
{
    public function testHydrate()
    {
        $model = new Model();
        $data = [
            'title' => 'Test',
            'event' => 1,
        ];

        $eventMock = $this->createMock(Event::class);

        $eventRepositoryMock = $this->createMock(EntityRepository::class);
        $eventRepositoryMock->expects($this->once())
            ->method('find')
            ->with($data['event'])
            ->willReturn($eventMock);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with('App\Entity\Event')
            ->willReturn($eventRepositoryMock);

        $modelHydrator = new ModelHydrator($entityManagerMock);

        $routeMock = $this->createMock(Route::class);
        $routeHydrator = $this->createMock(RouteHydrator::class);
        $routeHydrator->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf(Route::class))
            ->willReturn($routeMock);

        $modelHydrator->hydrate($data, $model, $routeHydrator);
        $this->assertEquals('Test', $model->getTitle());
        $this->assertSame($routeMock, $model->getRouteEntity());
        $this->assertSame($eventMock, $model->getEvent());
    }
}
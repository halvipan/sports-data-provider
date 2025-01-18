<?php

namespace App\Tests\Unit\Service;

use App\Entity\Model;
use App\Entity\Route;
use App\Repository\RouteRepository;
use App\Service\RouteMatcher;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouteMatcherTest extends TestCase
{
    public function testMatchRouteReturnsSimpleRoute(): void
    {
        $route1 = $this->createMock(Route::class);
        $route1->method('getRegexPattern')->willReturn('/hello/world');

        $model = $this->createMock(Model::class);
        $route1->method('getModel')->willReturn($model);

        $routeRepository = $this->createMock(RouteRepository::class);
        $routeRepository->method('findAll')->willReturn([$route1]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturn($routeRepository);

        $routeMatcher = new RouteMatcher($entityManager);

        $request = $this->createMock(Request::class);
        $request->method('getPathInfo')->willReturn('/hello/world');

        $result = $routeMatcher->matchRoute($request);

        $this->assertEquals([
            'parameters' => [],
            'model' => $model,
        ], $result);
    }

    public function testMatchRouteReturnsDynamicRoute(): void
    {
        $route1 = $this->createMock(Route::class);
        $route1->method('getRegexPattern')->willReturn('/hello/(?<name>\w+)');

        $model = $this->createMock(Model::class);
        $route1->method('getModel')->willReturn($model);

        $routeRepository = $this->createMock(RouteRepository::class);
        $routeRepository->method('findAll')->willReturn([$route1]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturn($routeRepository);

        $routeMatcher = new RouteMatcher($entityManager);

        $request = $this->createMock(Request::class);
        $request->method('getPathInfo')->willReturn('/hello/world');

        $result = $routeMatcher->matchRoute($request);

        $this->assertEquals([
            'parameters' => ['name' => 'world'],
            'model' => $model,
        ], $result);
    }

    public function testMatchRouteReturnsNull(): void
    {
        $routeRepository = $this->createMock(RouteRepository::class);
        $routeRepository->method('findAll')->willReturn([]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturn($routeRepository);

        $routeMatcher = new RouteMatcher($entityManager);

        $request = $this->createMock(Request::class);
        $request->method('getPathInfo')->willReturn('/hello/world');

        $result = $routeMatcher->matchRoute($request);

        $this->assertNull($result);
    }
}
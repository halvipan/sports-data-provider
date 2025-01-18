<?php

namespace App\Tests\Unit\Hydrator;

use App\Entity\Route;
use App\Hydrator\RouteHydrator;
use PHPUnit\Framework\TestCase;

class RouteHydratorTest extends TestCase
{
    public function testHydrate()
    {
        $routeHydrator = new RouteHydrator();
        $route = new Route();
        $data = [
            'path' => '/events/{id}',
        ];

        $routeHydrator->hydrate($data, $route);

        $this->assertEquals('/events/{id}', $route->getRouteTemplate());
        $this->assertEquals('/events/(?P<id>[^/]+)', $route->getRegexPattern());
        $this->assertEquals(['id'], $route->getVariables());
    }
}
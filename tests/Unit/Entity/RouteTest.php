<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testRoute()
    {
        $sut = new Route();

        $sut->setRouteTemplate('Test Route');
        $sut->setRegexPattern('Test Pattern');
        $sut->setVariables(['Test Variables']);

        $this->assertEquals('Test Route', $sut->getRouteTemplate());
        $this->assertEquals('Test Pattern', $sut->getRegexPattern());
        $this->assertEquals(['Test Variables'], $sut->getVariables());
        $this->assertNull($sut->getId());
        $this->assertNull($sut->getModel());
    }
}
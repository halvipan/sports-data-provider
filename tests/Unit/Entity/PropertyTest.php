<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Property;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    public function testProperty()
    {
        $property = new Property();
        $property->setPropertyKey('key');
        $property->setRouteValue('value');
        $property->setSimpleValue('value');
        $property->setEventDataKeyValue('value');

        $parent = $this->createMock(Property::class);
        $property->setParent($parent);

        $this->assertEquals('key', $property->getPropertyKey());
        $this->assertEquals('value', $property->getRouteValue());
        $this->assertEquals('value', $property->getSimpleValue());
        $this->assertEquals('value', $property->getEventDataKeyValue());
        $this->assertEquals($parent, $property->getParent());
        $this->assertNull($property->getId());

        $child = $this->createMock(Property::class);
        $property->addChild($child);
        $this->assertContains($child, $property->getChildren());

        $property->removeChild($child);
        $this->assertEmpty($property->getChildren());
    }

    public function testGetPropertyValue()
    {
        $property = new Property();
        $this->assertNull($property->getPropertyValue([]));

        $property->setSimpleValue('value');
        $this->assertEquals('value', $property->getPropertyValue([]));
        $property->setSimpleValue(null);

        $property->setRouteValue('key');
        $this->assertEquals('value', $property->getPropertyValue(['key' => 'value']));
        $property->setRouteValue(null);

        $property->setEventDataKeyValue('key');
        $this->assertEquals('value', $property->getPropertyValue(['key' => 'value']));

        $property->setEventDataKeyValue('key');
        $this->assertEquals(json_decode('{ "test": { "foo": "bar" } }', true), $property->getPropertyValue(['key' => '{ "test": { "foo": "bar" } }']));
        $property->setEventDataKeyValue(null);

        $childProperty = $this->createMock(Property::class);
        $childProperty->method('getPropertyValue')->willReturn(null);
        $property->addChild($childProperty);

        $childProperty1 = $this->createMock(Property::class);
        $childProperty1->method('getPropertyKey')->willReturn('key');
        $childProperty1->method('getPropertyValue')->willReturn('value');
        $property->addChild($childProperty1);
        $this->assertEquals(['key' => 'value'], $property->getPropertyValue([]));
    }
}
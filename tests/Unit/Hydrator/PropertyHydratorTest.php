<?php

namespace App\Tests\Unit\Hydrator;

use App\Entity\Property;
use App\Hydrator\PropertyHydrator;
use PHPUnit\Framework\TestCase;

class PropertyHydratorTest extends TestCase
{
    public function testHydrate()
    {
        $propertyHydrator = new PropertyHydrator();
        $property = new Property();

        $childProperty = $this->createMock(Property::class);
        $childProperty->method('getId')->willReturn(1);

        $property->addChild($childProperty);

        $data = [
            'properties' => [
                'key' => 'test',
                'simpleValue' => 'simpleValue',
                'routeVariableValue' => 'routeVariableValue',
                'eventDataKeyValue' => 'eventDataKeyValue',
                'children' => [
                    [
                        'key' => 'test',
                        'simpleValue' => 'simpleValue',
                        'routeVariableValue' => 'routeVariableValue',
                        'eventDataKeyValue' => 'eventDataKeyValue',
                        'children' => [
                            [
                                'key' => 'test',
                                'simpleValue' => 'simpleValue',
                                'routeVariableValue' => 'routeVariableValue',
                                'eventDataKeyValue' => 'eventDataKeyValue',
                            ]
                        ]
                    ],
                    [
                        'id' => 1,
                        'key' => 'test',
                        'simpleValue' => 'simpleValue',
                        'routeVariableValue' => 'routeVariableValue',
                        'eventDataKeyValue' => 'eventDataKeyValue',
                    ]
                ]
            ]
        ];

        $propertyHydrator->hydrate($data, $property);

        $this->assertEquals('test', $property->getPropertyKey());
        $this->assertEquals('simpleValue', $property->getSimpleValue());
        $this->assertEquals('routeVariableValue', $property->getRouteValue());
        $this->assertEquals('eventDataKeyValue', $property->getEventDataKeyValue());
        $this->assertCount(2, $property->getChildren());
    }
}
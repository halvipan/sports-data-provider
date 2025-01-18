<?php

namespace App\Tests\Unit\Hydrator;

use App\Entity\Event;
use App\Entity\EventData;
use App\Hydrator\EventDataHydrator;
use App\Hydrator\EventHydrator;
use PHPUnit\Framework\TestCase;

class EventHydratorTest extends TestCase
{
    public function testHydrate()
    {
        $hydrator = new EventHydrator();
        $event = new Event();
        $eventDataHydrator = $this->createMock(EventDataHydrator::class);
        $eventData = $this->createMock(EventData::class);

        $eventData->method('getId')->willReturn(1);
        $eventDataHydrator->method('hydrate')->willReturn($eventData);
        $data = [
            'title' => 'Test Event',
            'staticKeys' => ['key1', 'key2'],
            'evolvingKeys' => ['key3', 'key4'],
            'staticValues' => ['key1' => 'value1', 'key2' => 'value2'],
            'eventData' => [
                ['key3' => 1, 'key4' => 'value3'],
                ['id' => 1, 'key3' => 2, 'key4' => 'value4'],
            ],
        ];

        $hydrator->hydrate($data, $event, $eventDataHydrator);

        $this->assertEquals('Test Event', $event->getTitle());
        $this->assertEquals(['key1', 'key2'], $event->getStaticDataKeys());
        $this->assertEquals(['key3', 'key4'], $event->getEvolvingDataKeys());
        $this->assertEquals((object)['key1' => 'value1', 'key2' => 'value2'], $event->getStaticDataValues());
    }
}
<?php

namespace App\Tests\Unit\Hydrator;

use App\Entity\EventData;
use App\Hydrator\EventDataHydrator;
use DateTime;
use PHPUnit\Framework\TestCase;

class EventDataHydratorTest extends TestCase
{
    public function testHydrate()
    {
        $hydrator = new EventDataHydrator();
        $eventData = new EventData();
        $data = [
            'timeElapsed' => '00:10:20',
            'data' => [
                'this' => 'is',
                'a' => 'test'
            ]
        ];

        $hydrator->hydrate($data, $eventData);

        $this->assertEquals(new DateTime('00:10:20'), $eventData->getTimeElapsed());
        $this->assertEquals('is', $eventData->getData()['this']);
        $this->assertEquals('test', $eventData->getData()['a']);
    }
}
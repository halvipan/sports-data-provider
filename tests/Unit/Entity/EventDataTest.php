<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\EventData;
use PHPUnit\Framework\TestCase;

class EventDataTest extends TestCase
{
    public function testEventData()
    {
        $sut = new EventData();

        $sut->setTimeElapsed(new \DateTime());
        $sut->setData(['key1' => 'value1', 'key2' => 'value2']);
        $mockEvent = $this->createMock(Event::class);
        $sut->setEvent($mockEvent);

        $this->assertInstanceOf(\DateTime::class, $sut->getTimeElapsed());
        $this->assertEquals(['key1' => 'value1', 'key2' => 'value2'], $sut->getData());
        $this->assertEquals($mockEvent, $sut->getEvent());
        $this->assertNull($sut->getId());
    }
}
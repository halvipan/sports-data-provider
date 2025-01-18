<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\EventData;
use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testEvent()
    {
        $sut = new Event();
        $sut->setTitle('Test Event');
        $sut->setStaticDataKeys(['key1', 'key2']);
        $sut->setStaticDataValues((object) ['key1' => 'value1', 'key2' => 'value2']);
        $sut->setEvolvingDataKeys(['key3', 'key4']);

        $eventDataMock = $this->createMock(EventData::class);
        $eventDataMock->method('getEvent')->willReturn($sut);
        $sut->addEvolvingData($eventDataMock);

        $this->assertEquals('Test Event', $sut->getTitle());
        $this->assertEquals(['key1', 'key2'], $sut->getStaticDataKeys());
        $this->assertEquals((object) ['key1' => 'value1', 'key2' => 'value2'], $sut->getStaticDataValues());
        $this->assertEquals(['key3', 'key4'], $sut->getEvolvingDataKeys());
        $this->assertCount(1, $sut->getEvolvingData());
        $this->assertEquals($eventDataMock, $sut->getEvolvingData()->first());
        $this->assertNull($sut->getId());

        $sut->removeEvolvingData($eventDataMock);
        $this->assertCount(0, $sut->getEvolvingData());
    }

    public function testGetLiveEventData()
    {
        $sut = new Event();
        $eventDataMock = $this->createMock(EventData::class);
        $eventDataMock->method('getTimeElapsed')->willReturn(new DateTime());
        $sut->addEvolvingData($eventDataMock);

        $eventDataMock1 = $this->createMock(EventData::class);
        $eventDataMock1->method('getTimeElapsed')->willReturn((new DateTime())->add(new DateInterval('PT1M')));
        $sut->addEvolvingData($eventDataMock1);

        $this->assertEquals($eventDataMock, $sut->getLiveEventData(new DateTime()));
    }
}
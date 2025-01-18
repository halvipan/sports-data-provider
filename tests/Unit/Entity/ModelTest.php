<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\EventData;
use App\Entity\Model;
use App\Entity\Property;
use App\Entity\Route;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testModel()
    {
        $sut = new Model();

        $sut->setTitle('Test Model');
        $mockEvent = $this->createMock(Event::class);
        $sut->setEvent($mockEvent);
        $mockProperty = $this->createMock(Property::class);
        $sut->setRootProperty($mockProperty);
        $sut->setStartTime(new DateTime());
        $mockRouteEntity = $this->createMock(Route::class);
        $sut->setRouteEntity($mockRouteEntity);

        $this->assertEquals('Test Model', $sut->getTitle());
        $this->assertEquals($mockEvent, $sut->getEvent());
        $this->assertEquals($mockProperty, $sut->getRootProperty());
        $this->assertInstanceOf(\DateTime::class, $sut->getStartTime());
        $this->assertEquals($mockRouteEntity, $sut->getRouteEntity());
        $this->assertNull($sut->getId());
    }

    public function testGetLiveData()
    {
        $sut = new Model();
        $sut->setStartTime(new DateTime());
        $eventMock = $this->createMock(Event::class);
        $sut->setEvent($eventMock);

        $result = $sut->getLiveData((new DateTime())->add(new DateInterval('PT1M')), []);

        $this->assertEquals(['timeElapsed' => '00:01:00'], $result);
    }

    public function testGetLiveDataWithParameterTree()
    {
        $sut = new Model();
        $sut->setStartTime(new DateTime());
        $eventMock = $this->createMock(Event::class);
        $sut->setEvent($eventMock);
        $property = $this->createMock(Property::class);
        $childProperty = $this->createMock(Property::class);
        $childProperty->method('getPropertyKey')->willReturn('key');
        $childProperty->method('getPropertyValue')->willReturn('value');
        $collection = new ArrayCollection([$childProperty]);
        $property->method('getChildren')->willReturn($collection);
        $sut->setRootProperty($property);

        $result = $sut->getLiveData((new DateTime())->add(new DateInterval('PT1M')), []);

        $this->assertEquals('00:01:00', $result['timeElapsed']);
        $this->assertEquals('value', $result['key']);
    }

    public function testGetLiveDataWithAdditionalData()
    {
        $sut = new Model();
        $sut->setStartTime(new DateTime());
        $eventMock = $this->createMock(Event::class);
        $eventDataMock = $this->createMock(EventData::class);

        $eventMock->method('getLiveEventData')->willReturn($eventDataMock);
        $eventMock->method('getStaticDataValues')->willReturn((object) ['key' => 'value']);
        $sut->setEvent($eventMock);

        $result = $sut->getLiveData((new DateTime())->add(new DateInterval('PT1M')), ['key' => 'value']);

        $this->assertEquals(['timeElapsed' => '00:01:00'], $result);
    }
}
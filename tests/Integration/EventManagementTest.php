<?php

namespace App\Tests\Integration;

use App\Controller\EventController;
use App\Entity\Event;
use App\Entity\EventData;
use App\Hydrator\EventDataHydrator;
use App\Hydrator\EventHydrator;
use App\Repository\EventRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class EventManagementTest extends KernelTestCase
{
    public function testCreateNewEvent(): void
    {
        self::bootKernel();

        $eventController = self::getContainer()->get(EventController::class);
        assert($eventController instanceof EventController);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('persist')->willReturn(new Exception());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([]);
        $response = $eventController->createAction($request, $entityManagerMock, new EventHydrator());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Sorry, but something went wrong. Please try again later.', $response->getContent());


        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $eventController->createAction($getRequest, $entityManager, new EventHydrator());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([
            'title' => 'Test Event',
            'staticKeys' => ['skey1', 'skey2'],
            'evolvingKeys' => ['ekey3', 'ekey4'],
        ]);

        $eventController->createAction($request, $entityManager, new EventHydrator());

        $eventRepository = self::getContainer()->get(EventRepository::class);
        assert($eventRepository instanceof EventRepository);

        $eventEntity = $eventRepository->findBy(['title' => 'Test Event'])[0];
        $this->assertEquals('Test Event', $eventEntity->getTitle());
        $this->assertEquals(['skey1', 'skey2'], $eventEntity->getStaticDataKeys());
        $this->assertEquals(['ekey3', 'ekey4'], $eventEntity->getEvolvingDataKeys());

        $entityManager->rollback();
    }

    public function testEditEvent(): void
    {
        self::bootKernel();

        $eventController = self::getContainer()->get(EventController::class);
        assert($eventController instanceof EventController);

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $eventController->editAction($getRequest, $entityManager, new EventHydrator(), 10);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Sorry, but this entity does not exist, please return to the homepage', $response->getContent());

        $event = new Event();
        $event->setTitle('Test Event');
        $entityManager->persist($event);
        $entityManager->flush();
        $entityManager->refresh($event);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $eventController->editAction($getRequest, $entityManager, new EventHydrator(), $event->getId());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([
            'title' => 'Test Event',
            'staticKeys' => ['skey1', 'skey2'],
            'evolvingKeys' => ['ekey3', 'ekey4'],
        ]);

        $eventController->editAction($request, $entityManager, new EventHydrator(), $event->getId());

        $eventRepository = self::getContainer()->get(EventRepository::class);
        assert($eventRepository instanceof EventRepository);

        $eventEntity = $eventRepository->findBy(['title' => 'Test Event'])[0];
        $this->assertEquals('Test Event', $eventEntity->getTitle());
        $this->assertEquals(['skey1', 'skey2'], $eventEntity->getStaticDataKeys());
        $this->assertEquals(['ekey3', 'ekey4'], $eventEntity->getEvolvingDataKeys());

        $entityManager->rollback();
    }

    public function testAddStaticValuesToEvent(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $eventController = self::getContainer()->get(EventController::class);
        assert($eventController instanceof EventController);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $eventController->editStaticKeysAction($getRequest, $entityManager, new EventHydrator(), 10);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Sorry, but this entity does not exist, please return to the homepage', $response->getContent());

        $event = new Event();
        $event->setTitle('Test Event');
        $entityManager->persist($event);
        $entityManager->flush();
        $entityManager->refresh($event);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $eventController->editStaticKeysAction($getRequest, $entityManager, new EventHydrator(), $event->getId());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([
            'staticValues' => [
                'skey1' => 'svalue1',
                'skey2' => 'svalue2',
            ],
        ]);

        $eventRepository = self::getContainer()->get(EventRepository::class);
        assert($eventRepository instanceof EventRepository);

        $eventController->editStaticKeysAction($request, $entityManager, new EventHydrator(), $event->getId());

        $eventEntity = $eventRepository->findBy(['id' => $event])[0];
        $this->assertEquals((object) ['skey1' => 'svalue1', 'skey2' => 'svalue2',], $eventEntity->getStaticDataValues());

        $entityManager->rollback();
    }

    public function testAddDynamicValuesToEvent(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $eventController = self::getContainer()->get(EventController::class);
        assert($eventController instanceof EventController);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $eventController->editEvolvingKeysAction($getRequest, $entityManager, new EventHydrator(), new EventDataHydrator(), 10);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Sorry, but this entity does not exist, please return to the homepage', $response->getContent());


        $event = new Event();
        $event->setTitle('Test Event');
        $entityManager->persist($event);
        $entityManager->flush();
        $entityManager->refresh($event);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $eventController->editEvolvingKeysAction($getRequest, $entityManager, new EventHydrator(), new EventDataHydrator(), $event->getId());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([
            'eventData' => [
                [
                    'timeElapsed' => '00:00:00',
                    'data' => [
                        'ekey3' => '0',
                        'ekey4' => '0',
                    ]
                ],
                [
                    'timeElapsed' => '00:00:10',
                    'data' => [
                        'ekey3' => '0',
                        'ekey4' => '10',
                    ]
                ],
                [
                    'timeElapsed' => '00:10:10',
                    'data' => [
                        'ekey3' => '10',
                        'ekey4' => '10',
                    ]
                ]
            ],
        ]);

        $eventRepository = self::getContainer()->get(EventRepository::class);
        assert($eventRepository instanceof EventRepository);

        $eventController->editEvolvingKeysAction($request, $entityManager, new EventHydrator(), new EventDataHydrator(), $event->getId());

        $eventEntity = $eventRepository->findBy(['id' => $event])[0];
        $eventEntity->getEvolvingData()->map(function(EventData $eventData) {
            $this->assertInstanceOf(DateTime::class, $eventData->getTimeElapsed());
            $this->assertTrue(isset($eventData->getData()['ekey3']));
            $this->assertTrue(isset($eventData->getData()['ekey4']));
        });

        $entityManager->rollback();
    }
}
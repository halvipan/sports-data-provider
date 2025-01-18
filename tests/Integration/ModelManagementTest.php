<?php

namespace App\Tests\Integration;

use App\Controller\ModelController;
use App\Controller\PropertyController;
use App\Entity\Event;
use App\Entity\Model;
use App\Entity\Property;
use App\Hydrator\ModelHydrator;
use App\Hydrator\RouteHydrator;
use App\Repository\ModelRepository;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class ModelManagementTest extends KernelTestCase
{
    public function testCreateNewModel(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $event = new Event();
        $event->setTitle('Test Event');
        $entityManager->persist($event);
        $entityManager->flush();
        $entityManager->refresh($event);

        $modelController = self::getContainer()->get(ModelController::class);
        assert($modelController instanceof ModelController);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $modelController->createAction($getRequest, $entityManager, new ModelHydrator($entityManager), new RouteHydrator());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([
            'title' => 'Test Model',
            'path' => '/api/test/{variable}/route',
            'event' => $event->getId(),
        ]);

        $modelController->createAction($request, $entityManager, new ModelHydrator($entityManager), new RouteHydrator());

        $modelRepository = self::getContainer()->get(ModelRepository::class);
        assert($modelRepository instanceof ModelRepository);

        $modelEntity = $modelRepository->findBy(['title' => 'Test Model'])[0];
        $this->assertEquals('Test Model', $modelEntity->getTitle());
        $this->assertEquals('/api/test/{variable}/route', $modelEntity->getRouteEntity()->getRouteTemplate());
        $this->assertEquals('/api/test/(?P<variable>[^/]+)/route', $modelEntity->getRouteEntity()->getRegexPattern());
        $this->assertEquals(['variable'], $modelEntity->getRouteEntity()->getVariables());
        $this->assertEquals($event, $modelEntity->getEvent());
    }

    public function testEditModel(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $model = new Model();
        $model->setTitle('Test Model');

        $event = new Event();
        $event->setTitle('Test Event');
        $model->setEvent($event);

        $entityManager->persist($event);
        $entityManager->persist($model);
        $entityManager->flush();
        $entityManager->refresh($model);

        $modelController = self::getContainer()->get(ModelController::class);
        assert($modelController instanceof ModelController);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $modelController->editAction($getRequest, $entityManager, new ModelHydrator($entityManager), new RouteHydrator(), $model->getId());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([
            'title' => 'Test Model',
            'path' => '/api/test/{variable}/route',
            'event' => $event->getId(),
        ]);

        $modelController->editAction($request, $entityManager, new ModelHydrator($entityManager), new RouteHydrator(), $model->getId());

        $modelRepository = self::getContainer()->get(ModelRepository::class);
        assert($modelRepository instanceof ModelRepository);

        $modelEntity = $modelRepository->findBy(['title' => 'Test Model'])[0];
        $this->assertEquals('Test Model', $modelEntity->getTitle());
        $this->assertEquals('/api/test/{variable}/route', $modelEntity->getRouteEntity()->getRouteTemplate());
        $this->assertEquals('/api/test/(?P<variable>[^/]+)/route', $modelEntity->getRouteEntity()->getRegexPattern());
        $this->assertEquals(['variable'], $modelEntity->getRouteEntity()->getVariables());
        $this->assertEquals($event, $modelEntity->getEvent());
    }

    public function testPropertyTreeManagement(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $model = new Model();
        $model->setTitle('Test Model');

        $rootProperty = new Property();
        $rootProperty->setPropertyKey('root');
        $model->setRootProperty($rootProperty);

        $event = new Event();
        $event->setTitle('Test Event');
        $model->setEvent($event);

        $entityManager->persist($event);
        $entityManager->persist($model);
        $entityManager->flush();
        $entityManager->refresh($model);

        $propertyController = self::getContainer()->get(PropertyController::class);
        assert($propertyController instanceof PropertyController);

        $getRequest = $this->createMock(Request::class);
        $getRequest->method('isMethod')->willReturn(false);
        $response = $propertyController->editProperty($getRequest, $entityManager, $rootProperty->getId());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createMock(Request::class);
        $request->method('isMethod')->willReturn(true);
        $request->request = new InputBag([
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
                ]
            ]
        ]);

        $propertyController->editProperty($request, $entityManager, $rootProperty->getId());

        $propertyRepository = self::getContainer()->get(PropertyRepository::class);
        assert($propertyRepository instanceof PropertyRepository);

        $propertyEntity = $propertyRepository->findBy(['propertyKey' => 'test'])[0];
        $this->assertEquals('test', $propertyEntity->getPropertyKey());
        $this->assertEquals('simpleValue', $propertyEntity->getSimpleValue());
        $this->assertEquals('routeVariableValue', $propertyEntity->getRouteValue());
        $this->assertEquals('eventDataKeyValue', $propertyEntity->getEventDataKeyValue());
        $this->assertCount(1, $propertyEntity->getChildren());
    }
}
<?php

namespace App\Tests\Integration;

use App\Controller\ModelController;
use App\Controller\RouteController;
use App\Entity\Event;
use App\Entity\EventData;
use App\Entity\Model;
use App\Entity\Property;
use App\Entity\Route;
use App\Service\RouteMatcher;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class LiveEventRequestTest extends KernelTestCase
{
    public function testLiveEventRequest()
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->beginTransaction();

        $model = $this->createModel();
        $entityManager->persist($model->getEvent());
        $entityManager->persist($model);
        $entityManager->flush();
        $entityManager->refresh($model);

        $modelController = self::getContainer()->get(ModelController::class);
        assert($modelController instanceof ModelController);

        $response = $modelController->playModelAction($entityManager, $model->getId());
        $this->assertInstanceOf(DateTime::class, $model->getStartTime());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Model started', $response->getContent());

        $routeController = self::getContainer()->get(RouteController::class);
        assert($routeController instanceof RouteController);

        $request = $this->createMock(Request::class);
        $request->method('getPathInfo')->willReturn('/api/test/testRouteValue/route');

        $response = $routeController->routeAction($request, new RouteMatcher($entityManager));
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertArrayHasKey('timeElapsed', $content);
        $this->assertEquals('00:00:00', $content['timeElapsed']);
        $this->assertArrayHasKey('Test Simple Property', $content);
        $this->assertEquals('testSimpleValue', $content['Test Simple Property']);
        $this->assertArrayHasKey('Test Route Key Property', $content);
        $this->assertEquals('testRouteValue', $content['Test Route Key Property']);
        $this->assertArrayHasKey('Test Event Data Property', $content);
        $this->assertEquals('testEventDataValue', $content['Test Event Data Property']);
    }

    private function createModel(): Model
    {
        $model = new Model();
        $model->setTitle('Test Model');

        $event = new Event();
        $event->setTitle('Test Event');
        $event->setStaticDataValues((object)['testEventData' => 'testEventDataValue']);

        $eventData = new EventData();
        $eventData->setTimeElapsed(new DateTime('00:00:00'));
        $eventData->setData([]);
        $event->addEvolvingData($eventData);

        $model->setEvent($event);

        $simpleProperty = new Property();
        $simpleProperty->setPropertyKey('Test Simple Property');
        $simpleProperty->setSimpleValue('testSimpleValue');

        $routeKeyProperty = new Property();
        $routeKeyProperty->setPropertyKey('Test Route Key Property');
        $routeKeyProperty->setRouteValue('testRouteKey');

        $eventDataProperty = new Property();
        $eventDataProperty->setPropertyKey('Test Event Data Property');
        $eventDataProperty->setEventDataKeyValue('testEventData');

        $rootProperty = new Property();
        $rootProperty->setPropertyKey('Test Property');
        $rootProperty->addChild($simpleProperty);
        $rootProperty->addChild($routeKeyProperty);
        $rootProperty->addChild($eventDataProperty);
        $model->setRootProperty($rootProperty);

        $route = new Route();
        $route->setRouteTemplate('/api/test/{testRouteKey}/route');
        $route->setRegexPattern('/api/test/(?P<testRouteKey>[^/]+)/route');
        $route->setVariables(['testRouteKey']);
        $model->setRouteEntity($route);

        return $model;
    }
}
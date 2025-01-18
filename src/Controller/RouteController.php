<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RouteMatcher;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RouteController extends AbstractController
{
    #[Route(
        '{route}',
        name: 'app_route',
        requirements: ['route' => '.+'],
        condition: "service('app.route_matcher').matchRoute(request)"
    )]
    public function routeAction(Request $request, RouteMatcher $routeMatcher): JsonResponse
    {
        $match = $routeMatcher->matchRoute($request);

        $response = $match['model']->getLiveData(new DateTime(), $match['parameters']);

        return new JsonResponse($response);
    }
}

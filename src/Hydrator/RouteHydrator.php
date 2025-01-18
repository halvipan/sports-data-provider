<?php

namespace App\Hydrator;

use App\Entity\Route;

class RouteHydrator
{
    public function hydrate(array $data, Route $route): Route
    {
        $route->setRouteTemplate($data['path']);
        $route->setRegexPattern(preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $data['path']));
        $route->setVariables(preg_match_all('/\{(\w+)\}/', $data['path'], $matches) ? $matches[1] : []);

        return $route;
    }
}
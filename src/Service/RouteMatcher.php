<?php

namespace App\Service;

use App\Entity\Route;
use App\Trait\ExceptionMessageFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class RouteMatcher
{
    use ExceptionMessageFactory;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function matchRoute(Request $request): ?array
    {
        $routes = $this->entityManager->getRepository(Route::class)->findAll();

        foreach ($routes as $routeEntity) {
            $regexPattern = $routeEntity->getRegexPattern();

            if (preg_match("#^$regexPattern$#", $request->getPathInfo(), $matches)) {
                return [
                    'parameters' => array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY),
                    'model' => $routeEntity->getModel(),
                ];
            }
        }

        return null;
    }
}
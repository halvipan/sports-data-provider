<?php

namespace App\Hydrator;

use App\Entity\Event;
use App\Entity\Model;
use App\Entity\Route;
use Doctrine\ORM\EntityManagerInterface;

class ModelHydrator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function hydrate(array $data, Model $model, RouteHydrator $routeHydrator): Model
    {
        $model->setTitle($data['title']);
        $model->setRouteEntity($routeHydrator->hydrate($data, $model->getRouteEntity() ?? new Route()));
        $model->setEvent($this->entityManager->getRepository(Event::class)->find($data['event']));

        return $model;
    }
}
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Model;
use App\Hydrator\ModelHydrator;
use App\Hydrator\RouteHydrator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ModelController extends AbstractController
{
    #[Route('/model/create', name: 'app_model_create')]
    public function createAction(Request $request, EntityManagerInterface $entityManager, ModelHydrator $modelHydrator, RouteHydrator $routeHydrator): Response
    {
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();
            $model = $modelHydrator->hydrate($formData, new Model(), $routeHydrator);
            $entityManager->persist($model);
            $entityManager->flush();

            return $this->redirectToRoute('app_edit_property', ['propertyId' => $model->getRootProperty()->getId()]);
        }

        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('model/create.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/model/{modelId}/edit', name: 'app_model_edit')]
    public function editAction(Request $request, EntityManagerInterface $entityManager, ModelHydrator $modelHydrator, RouteHydrator $routeHydrator, int $modelId): Response
    {
        $model = $entityManager->getRepository(Model::class)->find($modelId);

        if ($request->isMethod('POST')) {
            $formData = $request->request->all();
            $modelHydrator->hydrate($formData, $model, $routeHydrator);
            $entityManager->flush();

            return $this->redirectToRoute('app_edit_property', ['propertyId' => $model->getRootProperty()->getId()]);
        }

        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('model/edit.html.twig', [
            'model' => $model,
            'events' => $events,
        ]);
    }

    #[Route('/model/{modelId}/play', name: 'app_model_play')]
    public function playModelAction(EntityManagerInterface $entityManager, int $modelId): Response
    {
        $model = $entityManager->getRepository(Model::class)->find($modelId);
        $model->setStartTime(new DateTime());
        $entityManager->flush();

        return new Response('Model started');
    }
}

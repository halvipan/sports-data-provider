<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Hydrator\EventDataHydrator;
use App\Hydrator\EventHydrator;
use App\Trait\ExceptionMessageFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    use ExceptionMessageFactory;
    #[Route('/event/create', name: 'app_event_create')]
    public function createAction(
        Request $request,
        EntityManagerInterface $entityManager,
        EventHydrator $eventHydrator
    ): Response
    {
        try {
            if ($request->isMethod('POST')) {
                $formData = $request->request->all();
                $event = $eventHydrator->hydrate($formData, new Event());
                $entityManager->persist($event);
                $entityManager->flush();

                return $this->redirectToRoute('app_event_edit_static_keys', [
                    'eventId' => $event->getId(),
                ]);
            }

            return $this->render('event/create.html.twig');
        } catch (\Exception $e) {
            return $this->render('error.html.twig', [
                'message' => $this->getExceptionMessageFor($e),
            ]);
        }
    }

    #[Route('/event/{eventId}/edit', name: 'app_event_edit')]
    public function editAction(
        Request $request,
        EntityManagerInterface $entityManager,
        EventHydrator $eventHydrator,
        int $eventId
    ): Response
    {
        try {
            $event = $entityManager->getRepository(Event::class)->find($eventId);

            if ($request->isMethod('POST')) {
                $formData = $request->request->all();
                $eventHydrator->hydrate($formData, $event);
                $entityManager->flush();

                return $this->redirectToRoute('app_event_edit_static_keys', [
                    'eventId' => $event->getId(),
                ]);
            }

            return $this->render('event/edit.html.twig', [
                'event' => $event,
            ]);
        } catch (\Exception $e) {
            return $this->render('error.html.twig', [
                'message' => $this->getExceptionMessageFor($e),
            ]);
        }
    }

    #[Route('/event/{eventId}/edit/staticKeys', name: 'app_event_edit_static_keys')]
    public function editStaticKeysAction(Request $request, EntityManagerInterface $entityManager, EventHydrator $eventHydrator, int $eventId): Response
    {
        try {
            $event = $entityManager->getRepository(Event::class)->find($eventId);

            if ($request->isMethod('POST')) {
                $formData = $request->request->all();
                $event = $eventHydrator->hydrate($formData, $event);
                $entityManager->flush();

                return $this->redirectToRoute('app_event_edit_evolving_keys', [
                    'eventId' => $event->getId(),
                ]);
            }

            return $this->render('event/edit/static_keys.html.twig', [
                'event' => $event,
            ]);
        } catch (\Exception $e) {
            return $this->render('error.html.twig', [
                'message' => $this->getExceptionMessageFor($e),
            ]);
        }
    }

    #[Route('/event/{eventId}/edit/evolvingKeys', name: 'app_event_edit_evolving_keys')]
    public function editEvolvingKeysAction(Request $request, EntityManagerInterface $entityManager, EventHydrator $eventHydrator, EventDataHydrator $eventDataHydrator, int $eventId): Response
    {
        try {
            $event = $entityManager->getRepository(Event::class)->find($eventId);

            if ($request->isMethod('POST')) {
                $formData = $request->request->all();
                $eventHydrator->hydrate($formData, $event, $eventDataHydrator);
                $entityManager->flush();

                return $this->redirectToRoute('app_index');
            }

            return $this->render('event/edit/evolving_keys.html.twig', [
                'event' => $event,
            ]);
        } catch (\Exception $e) {
            return $this->render('error.html.twig', [
                'message' => $this->getExceptionMessageFor($e),
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Model;
use App\Entity\Property;
use App\Hydrator\PropertyHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PropertyController extends AbstractController
{
    #[Route('/edit/property/{propertyId}', name: 'app_edit_property')]
    public function editProperty(Request $request, EntityManagerInterface $entityManager, int $propertyId): Response
    {
        $property = $entityManager->getRepository(Property::class)->find($propertyId);
        $model = $entityManager->getRepository(Model::class)->findBy(['rootProperty' => $property])[0];

        if ($request->isMethod('POST')) {
            $formData = $request->request->all();
            $propertyHydrator = new PropertyHydrator();
            $propertyHydrator->hydrate($formData, $property);
            $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('property/edit.html.twig', [
            'property' => $property,
            'routeVariables' => $model->getRouteEntity()?->getVariables(),
            'eventDataKeys' => array_merge($model->getEvent()->getEvolvingDataKeys(), $model->getEvent()->getStaticDataKeys()),
            'prototype' => new Property(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function indexAction(EntityManagerInterface $entityManager): Response
    {
        $models = $entityManager->getRepository(Model::class)->findAll();
        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('index/index.html.twig', [
            'indexController' => true,
            'models' => $models,
            'events' => $events,
        ]);
    }
}

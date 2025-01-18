<?php

namespace App\Tests\Integration;

use App\Controller\IndexController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VisitIndexTest extends KernelTestCase
{
    public function testVisitIndex()
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $indexController = self::getContainer()->get(IndexController::class);
        assert($indexController instanceof IndexController);

        $response = $indexController->indexAction($entityManager);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
<?php

namespace App\Tests\Unit\Repository;

use App\Repository\ModelRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class ModelRepositoryTest extends TestCase
{
    public function testRepository()
    {
        $mockManagerRegistry =  $this->createMock(ManagerRegistry::class);
        $repository = new ModelRepository($mockManagerRegistry);

        $this->assertInstanceOf(ModelRepository::class, $repository);
    }
}
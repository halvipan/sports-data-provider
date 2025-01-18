<?php

namespace App\Tests\Unit\Repository;

use App\Repository\PropertyRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class PropertyRepositoryTest extends TestCase
{
    public function testRepository()
    {
        $mockManagerRegistry =  $this->createMock(ManagerRegistry::class);
        $repository = new PropertyRepository($mockManagerRegistry);

        $this->assertInstanceOf(PropertyRepository::class, $repository);
    }
}
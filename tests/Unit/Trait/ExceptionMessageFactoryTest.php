<?php

namespace App\Tests\Unit\Trait;

use App\Trait\ExceptionMessageFactory;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ExceptionMessageFactoryTest extends TestCase
{

    public function exceptionMessageProvider()
    {
        return [
            [$this->createMock(UniqueConstraintViolationException::class), 'Sorry, action could not be completed as this entity already exists'],
            [$this->createMock(ConnectionException::class), 'Sorry, we were unable to save your changes, we are experiencing service issues. Please try again later.'],
            [$this->createMock(ForeignKeyConstraintViolationException::class), 'Sorry, we were unable to remove this entity, because it is used in other places.'],
            [$this->createMock(OptimisticLockException::class), 'Sorry, it appears someone else has already changed this entity. Please refresh the page and try again.'],
            [$this->createMock(EntityNotFoundException::class), 'Sorry, but this entity does not exist, please return to the homepage.'],
            [$this->createMock(Exception::class), 'Sorry, but something went wrong. Please try again later.'],
        ];
    }

    /**
     * @dataProvider exceptionMessageProvider
     */
    public function testGetExceptionMessageForException(Exception $exception, string $expectedMessage)
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $exceptionMessageFactory = new class($loggerMock) {use ExceptionMessageFactory; };

        $message = $exceptionMessageFactory->getExceptionMessageFor($exception);
        $this->assertEquals($expectedMessage, $message);
    }
}
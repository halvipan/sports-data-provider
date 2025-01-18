<?php

namespace App\Trait;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Psr\Log\LoggerInterface;

trait ExceptionMessageFactory
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function getExceptionMessageFor(Exception $exception): string
    {
        $this->logger->error('Doctrine exception occurred:', ['exception' => $exception]);

        $exceptionMessages = [
            UniqueConstraintViolationException::class => 'Sorry, action could not be completed as this entity already exists',
            ConnectionException::class => 'Sorry, we were unable to save your changes, we are experiencing service issues. Please try again later.',
            ForeignKeyConstraintViolationException::class => 'Sorry, we were unable to remove this entity, because it is used in other places.',
            OptimisticLockException::class => 'Sorry, it appears someone else has already changed this entity. Please refresh the page and try again.',
            EntityNotFoundException::class => 'Sorry, but this entity does not exist, please return to the homepage.',
        ];

        foreach ($exceptionMessages as $exceptionClass => $userMessage) {
            if ($exception instanceof $exceptionClass) {
                return $userMessage;
            }
        }

        return 'Sorry, but something went wrong. Please try again later.';
    }
}
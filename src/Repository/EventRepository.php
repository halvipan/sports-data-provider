<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

        /**
         * @inheritDoc
         */
        public function find(mixed $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null): ?object
        {
            $event = parent::find($id, $lockMode, $lockVersion);

            if (is_null($event)) {
                throw new EntityNotFoundException();
            }

            return $event;
        }
}

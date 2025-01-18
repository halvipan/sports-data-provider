<?php

namespace App\Hydrator;

use App\Entity\EventData;
use DateTime;

class EventDataHydrator
{
    public function hydrate(array $data, EventData $eventData): EventData
    {
        if (isset($data['timeElapsed'])) $eventData->setTimeElapsed(new DateTime($data['timeElapsed']));
        if (isset($data['data'])) $eventData->setData($data['data']);

        return $eventData;
    }
}
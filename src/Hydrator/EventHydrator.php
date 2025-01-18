<?php

namespace App\Hydrator;

use App\Entity\Event;
use App\Entity\EventData;

class EventHydrator
{
    public function hydrate(array $data, Event $event, ?EventDataHydrator $eventDataHydrator = null): Event
    {
        if (isset($data['title'])) $event->setTitle($data['title']);
        if (isset($data['staticKeys'])) $event->setStaticDataKeys($data['staticKeys']);
        if (isset($data['evolvingKeys'])) $event->setEvolvingDataKeys($data['evolvingKeys']);
        if (isset($data['staticValues'])) $event->setStaticDataValues((object)$data['staticValues']);
        if (isset($data['eventData'])) $this->setEvolvingData($data, $event, $eventDataHydrator);

        return $event;
    }

    public function setEvolvingData(array $data, Event $event, EventDataHydrator $eventDataHydrator): Event
    {
        foreach ($data['eventData'] as $eventData) {
            if (isset($eventData['id'])) {
                $childEventData = $event->getEvolvingData()->filter(function($child) use ($eventData) {
                    return $child->getId() == $eventData['id'];
                })->first();
            } else {
                $childEventData = new EventData();
            }
            $event->addEvolvingData($eventDataHydrator->hydrate($eventData, $childEventData));
        }

        return $event;
    }
}
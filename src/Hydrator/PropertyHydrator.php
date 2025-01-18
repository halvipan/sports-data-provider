<?php

namespace App\Hydrator;

use App\Entity\Property;

class PropertyHydrator
{
    public function hydrate(array $data, Property $rootProperty): Property
    {
        return $this->hydrateProperty($rootProperty, $data['properties']);
    }

    private function hydrateProperty(Property $property, array $data): Property
    {
        $property->setPropertyKey($data['key'] ?? '');
        if (isset($data['simpleValue']) && $data['simpleValue'] != '') $property->setSimpleValue($data['simpleValue']);
        if (isset($data['routeVariableValue']) && $data['routeVariableValue'] != '') $property->setRouteValue($data['routeVariableValue']);
        if (isset($data['eventDataKeyValue']) && $data['eventDataKeyValue'] != '') $property->setEventDataKeyValue($data['eventDataKeyValue']);

        if (isset($data['children'])) {
            foreach ($data['children'] as $childData) {
                if (isset($childData['id'])) {
                    $childProperty = $property->getChildren()->filter(function($child) use ($childData) {
                        return $child->getId() == $childData['id'];
                    })->first();
                } else {
                    $childProperty = new Property();
                }
                $this->hydrateProperty($childProperty, $childData);
                $property->addChild($childProperty);
            }
        }

        return $property;
    }
}
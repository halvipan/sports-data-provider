<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $propertyKey = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $simpleValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $routeValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $eventDataKeyValue = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent', cascade: ['persist', 'remove'])]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPropertyKey(): ?string
    {
        return $this->propertyKey;
    }

    public function setPropertyKey(string $propertyKey): static
    {
        $this->propertyKey = $propertyKey;

        return $this;
    }

    public function getSimpleValue(): ?string
    {
        return $this->simpleValue;
    }

    public function setSimpleValue(?string $simpleValue): static
    {
        $this->simpleValue = $simpleValue;

        return $this;
    }

    public function getRouteValue(): ?string
    {
        return $this->routeValue;
    }

    public function setRouteValue(?string $routeValue): static
    {
        $this->routeValue = $routeValue;

        return $this;
    }

    public function getEventDataKeyValue(): ?string
    {
        return $this->eventDataKeyValue;
    }

    public function setEventDataKeyValue(?string $eventDataKeyValue): static
    {
        $this->eventDataKeyValue = $eventDataKeyValue;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        $this->children->removeElement($child);

        return $this;
    }

    public function getPropertyValue(array $data): mixed
    {

        if (isset($this->simpleValue) && $this->simpleValue != '') return $this->simpleValue;
        if (isset($this->routeValue) && $this->routeValue != '') return $data[$this->routeValue];
        if (isset($this->eventDataKeyValue) && $this->eventDataKeyValue != '') {
            if (json_validate($data[$this->eventDataKeyValue])) {
                return json_decode($data[$this->eventDataKeyValue], true);
            }
            return $data[$this->eventDataKeyValue];
        }

        if ($this->children->count() > 0) {
            $result = [];
            foreach ($this->children as $child) {
                if (is_null($child->getPropertyValue($data))) {
                    continue;
                }
                $result[$child->getPropertyKey()] = $child->getPropertyValue($data);
            }
            return $result;
        }
        return null;
    }
}

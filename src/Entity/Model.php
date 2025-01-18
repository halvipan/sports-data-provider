<?php

namespace App\Entity;

use App\Repository\ModelRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use http\Env\Request;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $route = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Property $rootProperty = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\OneToOne(inversedBy: 'model', cascade: ['persist', 'remove'])]
    private ?Route $routeEntity = null;

    public function __construct()
    {
        $this->rootProperty = new Property();
        $this->rootProperty->setPropertyKey('root');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getRootProperty(): ?Property
    {
        return $this->rootProperty;
    }

    public function setRootProperty(?Property $rootProperty): static
    {
        $this->rootProperty = $rootProperty;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getLiveData(DateTime $requestTime, array $additionalData): array
    {
        $timeElapsed = $requestTime->diff($this->startTime);
        $baseTime = new DateTime('00:00:00');
        $elapsedDateTime = (clone $baseTime)->sub($timeElapsed);

        $liveEventData = $this->event->getLiveEventData($elapsedDateTime);
        $data = array_merge($liveEventData->getData(),$additionalData);

        if ($this->getEvent() && $this->getEvent()->getStaticDataValues())
        {
            $data = array_merge($data, (array)$this->getEvent()->getStaticDataValues());
        }

        $result = [];
        $result['timeElapsed'] = $timeElapsed->format('%H:%I:%S');

        foreach ($this->rootProperty->getChildren() as $child) {
            $result[$child->getPropertyKey()] = $child->getPropertyValue($data);
        }

        return $result;
    }

    public function getRouteEntity(): ?Route
    {
        return $this->routeEntity;
    }

    public function setRouteEntity(?Route $routeEntity): static
    {
        $this->routeEntity = $routeEntity;

        return $this;
    }
}

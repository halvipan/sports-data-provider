<?php

namespace App\Entity;

use App\Repository\EventDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventDataRepository::class)]
class EventData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timeElapsed = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $data = [];

    #[ORM\ManyToOne(inversedBy: 'evolvingData')]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeElapsed(): ?\DateTimeInterface
    {
        return $this->timeElapsed;
    }

    public function setTimeElapsed(\DateTimeInterface $timeElapsed): static
    {
        $this->timeElapsed = $timeElapsed;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

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
}

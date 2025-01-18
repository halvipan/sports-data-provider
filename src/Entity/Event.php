<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $staticDataKeys = [];

    #[ORM\Column(type: Types::OBJECT, nullable: true)]
    private ?object $staticDataValues = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $evolvingDataKeys = [];

    /**
     * @var Collection<int, EventData>
     */
    #[ORM\OneToMany(targetEntity: EventData::class, mappedBy: 'event', cascade: ['persist'])]
    private Collection $evolvingData;

    public function __construct()
    {
        $this->evolvingData = new ArrayCollection();
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

    public function getStaticDataKeys(): array
    {
        return $this->staticDataKeys;
    }

    public function setStaticDataKeys(array $staticDataKeys): static
    {
        $this->staticDataKeys = $staticDataKeys;

        return $this;
    }

    public function getStaticDataValues(): ?object
    {
        return $this->staticDataValues;
    }

    public function setStaticDataValues(?object $staticDataValues): static
    {
        $this->staticDataValues = $staticDataValues;

        return $this;
    }

    public function getEvolvingDataKeys(): array
    {
        return $this->evolvingDataKeys;
    }

    public function setEvolvingDataKeys(array $evolvingDataKeys): static
    {
        $this->evolvingDataKeys = $evolvingDataKeys;

        return $this;
    }

    /**
     * @return Collection<int, EventData>
     */
    public function getEvolvingData(): Collection
    {
        return $this->evolvingData;
    }

    public function getLiveEventData($elapsedDateTime): EventData
    {
        $lastEvolvingData = null;
        foreach ($this->getEvolvingData() as $evolvingData) {
            if ($evolvingData->getTimeElapsed() < $elapsedDateTime) {
                $lastEvolvingData = $evolvingData;
            }
            if ($evolvingData->getTimeElapsed() > $elapsedDateTime) {
                break;
            }
        }

        return $lastEvolvingData;
    }

    public function addEvolvingData(EventData $evolvingData): static
    {
        if (!$this->evolvingData->contains($evolvingData)) {
            $this->evolvingData->add($evolvingData);
            $evolvingData->setEvent($this);
        }

        return $this;
    }

    public function removeEvolvingData(EventData $evolvingData): static
    {
        if ($this->evolvingData->removeElement($evolvingData)) {
            // set the owning side to null (unless already changed)
            if ($evolvingData->getEvent() === $this) {
                $evolvingData->setEvent(null);
            }
        }

        return $this;
    }
}

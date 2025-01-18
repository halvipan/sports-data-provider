<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $routeTemplate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $regexPattern = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $variables = null;

    #[ORM\OneToOne(mappedBy: 'routeEntity', cascade: ['persist', 'remove'])]
    private ?Model $model = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRouteTemplate(): ?string
    {
        return $this->routeTemplate;
    }

    public function setRouteTemplate(string $routeTemplate): static
    {
        $this->routeTemplate = $routeTemplate;

        return $this;
    }

    public function getRegexPattern(): ?string
    {
        return $this->regexPattern;
    }

    public function setRegexPattern(?string $regexPattern): static
    {
        $this->regexPattern = $regexPattern;

        return $this;
    }

    public function getVariables(): ?array
    {
        return $this->variables;
    }

    public function setVariables(?array $variables): static
    {
        $this->variables = $variables;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }
}

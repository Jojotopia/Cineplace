<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

//Les autres fonctions ne sont pas utilisé car gérer par EasyAdmin
#[ApiResource(
    collectionOperations: ["get" => ['normalization_context' => ['groups' => ['reservation']]]],
    itemOperations: ["get" => ['normalization_context' => ['groups' => ['reservation']]]],
    order: ["projection.date" => "DESC"] )]
#[ApiFilter(SearchFilter::class, properties: ['client.email' => 'exact'])]//Permet de rechercher une reservation via un email
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups("reservation")]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Groups("reservation")]
    private $nbSieges;

    #[ORM\ManyToOne(targetEntity: Projection::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("reservation")]
    private $projection;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'reservations')]
    #[Groups("reservation")]
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbSieges(): ?int
    {
        return $this->nbSieges;
    }

    public function setNbSieges(int $nbSieges): self
    {
        $this->nbSieges = $nbSieges;

        return $this;
    }

    public function getProjection(): ?Projection
    {
        return $this->projection;
    }

    public function setProjection(?Projection $projection): self
    {
        $this->projection = $projection;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}

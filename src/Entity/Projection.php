<?php

namespace App\Entity;

use App\Repository\ProjectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use IntlDateFormatter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

//Les autres fonctions ne sont pas utilisé car gérer par EasyAdmin
#[ApiResource(
    collectionOperations: [],
    itemOperations: ["get"] )]
#[ORM\Entity(repositoryClass: ProjectionRepository::class)]
class Projection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["reservation", "film"])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Groups(["reservation", "film"])]
    private $nbPlaces;

    #[ORM\Column(type: 'integer')]
    #[Groups(["reservation", "film"])]
    private $nbPlaceDispo;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["reservation", "film"])]
    private $date;

    #[ORM\ManyToOne(targetEntity: Film::class, inversedBy: 'projections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("reservation")]
    private $film;

    #[ORM\OneToMany(mappedBy: 'projection', targetEntity: Reservation::class, cascade: ["remove"])]
    private $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): self
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function getNbPlaceDispo(): ?int
    {
        return $this->nbPlaceDispo;
    }

    public function setNbPlaceDispo(int $nbPlaceDispo): self
    {
        $this->nbPlaceDispo = $nbPlaceDispo;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): self
    {
        $this->film = $film;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setProjection($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getProjection() === $this) {
                $reservation->setProjection(null);
            }
        }

        return $this;
    }
}

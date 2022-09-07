<?php

namespace App\Entity;

use App\Repository\RealisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

//Les autres fonctions ne sont pas utilisé car gérer par EasyAdmin
#[ApiResource(
    collectionOperations: [],
    itemOperations: ["get"] )]
#[ORM\Entity(repositoryClass: RealisateurRepository::class)]
class Realisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups("film")]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups("film")]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups("film")]
    private $prenom;

    #[ORM\ManyToMany(targetEntity: Film::class, inversedBy: 'realisateurs')]
    private $film;

    public function __construct()
    {
        $this->film = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Collection<int, Film>
     */
    public function getFilm(): Collection
    {
        return $this->film;
    }

    public function addFilm(Film $film): self
    {
        if (!$this->film->contains($film)) {
            $this->film[] = $film;
        }

        return $this;
    }

    public function removeFilm(Film $film): self
    {
        $this->film->removeElement($film);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
    	return "$this->prenom $this->nom";
    }
}

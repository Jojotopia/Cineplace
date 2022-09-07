<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

//Les autres fonctions ne sont pas utilisé car gérer par EasyAdmin
#[ApiResource(
    normalizationContext: ['groups' => ['categorie']],
    collectionOperations: ["get"],
    itemOperations: ["get"] )]
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["film", "categorie"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["film", "categorie"])]
    private $nom;

    #[ORM\ManyToMany(targetEntity: Film::class, mappedBy: 'categorie')]
    #[Groups(["categorie"])]
    private $films;

    public function __construct()
    {
        $this->films = new ArrayCollection();
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
    
    /**
     * @return string
     */
    public function __toString() {
    	return "$this->nom";
    }

    /**
     * @return Collection<int, Film>
     */
    public function getFilms(): Collection
    {
        return $this->films;
    }

    public function addFilm(Film $film): self
    {
        if (!$this->films->contains($film)) {
            $this->films[] = $film;
            $film->addCategorie($this);
        }

        return $this;
    }

    public function removeFilm(Film $film): self
    {
        if ($this->films->removeElement($film)) {
            $film->removeCategorie($this);
        }

        return $this;
    }
}

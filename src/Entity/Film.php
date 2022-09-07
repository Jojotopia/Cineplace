<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
//Les autres fonctions ne sont pas utilisé car gérer par EasyAdmin
#[ApiResource(
    normalizationContext: ['groups' => ['film']],
    collectionOperations: ["get"],
    itemOperations: ["get"] )]
#[ApiFilter(OrderFilter::class, properties: ['id'], arguments: ['orderParameterName' => 'order'])]
/**
 * @Vich\Uploadable
 */
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["film", "categorie", "reservation"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["film", "categorie", "reservation"])]
    private $titre;

    #[ORM\Column(type: 'text')]
    #[Groups(["film", "categorie"])]
    private $resume;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["film", "categorie", "reservation"])]
    private $affiche;
    
    /**
     * @Vich\UploadableField(mapping="film_images", fileNameProperty="affiche")
     */
    private $afficheFile;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'integer')]
    #[Groups("film")]
    private $restriction_age;

    #[ORM\Column(type: 'time')]
    #[Groups("film")]
    private $duree;

    #[ORM\Column(type: 'datetime')]
    #[Groups("film")]
    private $date_sortie;

    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'films')]
    #[Groups("film")]
    private $categorie;

    #[ORM\ManyToMany(targetEntity: Acteur::class, mappedBy: 'film')]
    #[Groups("film")]
    private $acteurs;

    #[ORM\ManyToMany(targetEntity: Realisateur::class, mappedBy: 'film')]
    #[Groups("film")]
    private $realisateurs;

    #[ORM\OneToMany(mappedBy: 'film', targetEntity: Projection::class)]
    #[Groups("film")]
    private $projections;

    public function __construct()
    {
        $this->categorie = new ArrayCollection();
        $this->acteurs = new ArrayCollection();
        $this->realisateurs = new ArrayCollection();
        $this->projections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getAffiche(): ?string
    {
        return $this->affiche;
    }

    public function setAffiche(string|null $affiche): self
    {
        $this->affiche = $affiche;

        return $this;
    }

    public function setAfficheFile(File $afficheFile = null): void
    {
        $this->afficheFile = $afficheFile;

        if (null !== $afficheFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAfficheFile(): ?File
    {
        return $this->afficheFile;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRestrictionAge(): ?int
    {
        return $this->restriction_age;
    }

    public function setRestrictionAge(int $restriction_age): self
    {
        $this->restriction_age = $restriction_age;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->date_sortie;
    }

    public function setDateSortie(\DateTimeInterface $date_sortie): self
    {
        $this->date_sortie = $date_sortie;

        return $this;
    }

    //Permet de limiter le résumé en nombre de caractères
    public function resume_tronquer(){
        $longeur_max = 300;
        $texte = $this->resume;
        if (strlen($texte) > $longeur_max){
            $texte = mb_substr($texte, 0, $longeur_max,'UTF-8');
            $texte .= "...";
        }
        return $texte;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }

    /**
     * @return Collection<int, Acteur>
     */
    public function getActeurs(): Collection
    {
        return $this->acteurs;
    }

    public function addActeur(Acteur $acteur): self
    {
        if (!$this->acteurs->contains($acteur)) {
            $this->acteurs[] = $acteur;
            $acteur->addFilm($this);
        }

        return $this;
    }

    public function removeActeur(Acteur $acteur): self
    {
        if ($this->acteurs->removeElement($acteur)) {
            $acteur->removeFilm($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Realisateur>
     */
    public function getRealisateurs(): Collection
    {
        return $this->realisateurs;
    }

    public function addRealisateur(Realisateur $realisateur): self
    {
        if (!$this->realisateurs->contains($realisateur)) {
            $this->realisateurs[] = $realisateur;
            $realisateur->addFilm($this);
        }

        return $this;
    }

    public function removeRealisateur(Realisateur $realisateur): self
    {
        if ($this->realisateurs->removeElement($realisateur)) {
            $realisateur->removeFilm($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Projection>
     */
    public function getProjections(): Collection
    {
        return $this->projections;
    }

    public function addProjection(Projection $projection): self
    {
        if (!$this->projections->contains($projection)) {
            $this->projections[] = $projection;
            $projection->setFilm($this);
        }

        return $this;
    }

    public function removeProjection(Projection $projection): self
    {
        if ($this->projections->removeElement($projection)) {
            // set the owning side to null (unless already changed)
            if ($projection->getFilm() === $this) {
                $projection->setFilm(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
    	return $this->titre;
    }
}

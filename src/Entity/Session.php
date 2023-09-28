<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_session = null;

    #[ORM\Column]
    private ?int $nbPlace = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateSession = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Programme::class, orphanRemoval: true)]
    private Collection $programmes;

    #[ORM\ManyToMany(targetEntity: Stagiaire::class, mappedBy: 'sessions')]
    private Collection $stagiaires;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formateur $formateur = null;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
        $this->stagiaires = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getNomSession();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour calculer la capacité restante
    public function getCapacite(): int
    {
        if ($this->getNbPlace() !== null) {
            return $this->getNbPlace() - $this->getStagiaires()->count();
        }
        
        return 0; // Par défaut, retournez 0 si nbPlace n'est pas défini.
    }

    // Méthode pour récupérer le nombre de stagiaires inscrits à une session
    public function getNbStagiaires(): int
    {
        return $this->getStagiaires()->count();
    }


    public function getNomSession(): ?string
    {
        return $this->nom_session;
    }

    public function setNomSession(string $nom_session): static
    {
        $this->nom_session = $nom_session;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): static
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getDateSession(): ?\DateTimeInterface
    {
        return $this->dateSession;
    }

    // Méthode pour calculer la durée d'une session
    public function getDureeJours(): int
    {
        $dateDebut = $this->getDateSession();
        $dateFin = $this->getDateFin();

        if ($dateDebut !== null && $dateFin !== null) {
            $diff = $dateDebut->diff($dateFin);
            return $diff->days;
        }

        return 0; // Par défaut, retournez 0 si dateDebut ou dateFin n'est pas défini.
    }

    public function setDateSession(\DateTimeInterface $dateSession): static
    {
        $this->dateSession = $dateSession;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * @return Collection<int, Programme>
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programme $programme): static
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->setSession($this);
        }

        return $this;
    }

    public function removeProgramme(Programme $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getSession() === $this) {
                $programme->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stagiaire>
     */
    public function getStagiaires(): Collection
    {
        return $this->stagiaires;
    }

    public function addStagiaire(Stagiaire $stagiaire): static
    {
        if (!$this->stagiaires->contains($stagiaire)) {
            $this->stagiaires->add($stagiaire);
            $stagiaire->addSession($this);
        }

        return $this;
    }

    public function removeStagiaire(Stagiaire $stagiaire): static
    {
        if ($this->stagiaires->removeElement($stagiaire)) {
            $stagiaire->removeSession($this);
        }

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): static
    {
        $this->formateur = $formateur;

        return $this;
    }
}

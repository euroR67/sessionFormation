<?php

namespace App\Entity;

use App\Repository\ProgrammeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeRepository::class)]
class Programme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $dureeJour = null;

    #[ORM\ManyToOne(inversedBy: 'programmes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'programmes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $module = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        $module = $this->getModule();
        return $module->getNomModule();
    }

    public function getDureeJour(): ?int
    {
        return $this->dureeJour;
    }

    public function setDureeJour(int $dureeJour): static
    {
        $this->dureeJour = $dureeJour;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getModule(): ?Modules
    {
        return $this->module;
    }

    public function setModule(?Modules $module): static
    {
        $this->module = $module;

        return $this;
    }
}

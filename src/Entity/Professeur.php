<?php

namespace App\Entity;

use App\Repository\ProfesseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfesseurRepository::class)
 */
class Professeur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $arpege;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomProfesseur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomProfesseur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailProfesseur;

    /**
     * @ORM\ManyToMany(targetEntity=Matiere::class, inversedBy="professeurs")
     */
    private $matieres;

    /**
     * @ORM\OneToMany(targetEntity=Seance::class, mappedBy="professeur")
     */
    private $seances;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateNaissance;

    public function __construct()
    {
        $this->matieres = new ArrayCollection();
        $this->seances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArpege(): ?string
    {
        return $this->arpege;
    }

    public function setArpege(string $arpege): self
    {
        $this->arpege = $arpege;

        return $this;
    }

    public function getPrenomProfesseur(): ?string
    {
        return $this->prenomProfesseur;
    }

    public function setPrenomProfesseur(string $prenomProfesseur): self
    {
        $this->prenomProfesseur = $prenomProfesseur;

        return $this;
    }

    public function getNomProfesseur(): ?string
    {
        return $this->nomProfesseur;
    }

    public function setNomProfesseur(string $nomProfesseur): self
    {
        $this->nomProfesseur = $nomProfesseur;

        return $this;
    }

    public function getEmailProfesseur(): ?string
    {
        return $this->emailProfesseur;
    }

    public function setEmailProfesseur(?string $emailProfesseur): self
    {
        $this->emailProfesseur = $emailProfesseur;

        return $this;
    }

    /**
     * @return Collection|Matiere[]
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matiere $matiere): self
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres[] = $matiere;
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): self
    {
        $this->matieres->removeElement($matiere);

        return $this;
    }

    /**
     * @return Collection|Seance[]
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->seances->contains($seance)) {
            $this->seances[] = $seance;
            $seance->setProfesseur($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        if ($this->seances->removeElement($seance)) {
            // set the owning side to null (unless already changed)
            if ($seance->getProfesseur() === $this) {
                $seance->setProfesseur(null);
            }
        }

        return $this;
    }

    public function getDateNaissance(): ?string
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(string $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }
}

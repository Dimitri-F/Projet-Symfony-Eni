<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'etat', targetEntity: sortie::class)]
    private Collection $etat;

    public function __construct()
    {
        $this->etat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, sortie>
     */
    public function getEtat(): Collection
    {
        return $this->etat;
    }

    public function addEtat(sortie $etat): static
    {
        if (!$this->etat->contains($etat)) {
            $this->etat->add($etat);
            $etat->setEtat($this);
        }

        return $this;
    }

    public function removeEtat(sortie $etat): static
    {
        if ($this->etat->removeElement($etat)) {
            // set the owning side to null (unless already changed)
            if ($etat->getEtat() === $this) {
                $etat->setEtat(null);
            }
        }

        return $this;
    }
}

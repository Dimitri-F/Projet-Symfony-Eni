<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message : "Le nom ne peut pas être vide" )]
    #[Assert\Length(min : 3, max: 30,
        minMessage : "Le nom doit avoir un minimum de 3 caractères",
        maxMessage : "Le nom doit avoir un maximum de 30 caractères")]
    private ?string $nom = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message : "Le code postal ne peut pas être vide" )]
    #[Assert\Regex(pattern : "/^[A-Za-z0-9]$/",
                   message : "Le code postal doit contenir que des lettres et des chiffres",
                   match : false )]
    #[Assert\Length(min : 5, max : 10,
        minMessage : "Le code postal doit avoir un minimum de 5 caractères",
        maxMessage : "Le code postal doit avoir un maximum de 10 caractères")]
    private ?string $codePostal = null;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: lieu::class)]
    private Collection $lieux;

    public function __construct()
    {
        $this->lieux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, lieu>
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
    }

    public function addLieux(lieu $lieux): static
    {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux->add($lieux);
            $lieux->setVille($this);
        }

        return $this;
    }

    public function removeLieux(lieu $lieux): static
    {
        if ($this->lieux->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getVille() === $this) {
                $lieux->setVille(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\WinnedCombinationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WinnedCombinationRepository::class)
 */
class WinnedCombination
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="winnedCombinations")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="json")
     */
    private $combination = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCombination(): ?array
    {
        return $this->combination;
    }

    public function setCombination(array $combination): self
    {
        $this->combination = $combination;

        return $this;
    }
}

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
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="winnedCombinations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="json")
     */
    private $combination = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

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

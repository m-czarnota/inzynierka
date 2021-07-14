<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScoreRepository::class)
 */
class Score
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Player::class, inversedBy="score", cascade={"persist", "remove"})
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="scores")
     */
    private $season;

    /**
     * @ORM\Column(type="float")
     */
    private $ranks;

    /**
     * @ORM\Column(type="float")
     */
    private $withComputer;

    /**
     * @ORM\Column(type="float")
     */
    private $withFriend;

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

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getRanks(): ?float
    {
        return $this->ranks;
    }

    public function setRanks(float $ranks): self
    {
        $this->ranks = $ranks;

        return $this;
    }

    public function getWithComputer(): ?float
    {
        return $this->withComputer;
    }

    public function setWithComputer(float $withComputer): self
    {
        $this->withComputer = $withComputer;

        return $this;
    }

    public function getWithFriend(): ?float
    {
        return $this->withFriend;
    }

    public function setWithFriend(float $withFriend): self
    {
        $this->withFriend = $withFriend;

        return $this;
    }
}

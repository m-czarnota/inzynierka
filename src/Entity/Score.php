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
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="score", cascade={"persist", "remove"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="scores")
     */
    private ?Season $season;

    /**
     * @ORM\Column(type="float")
     */
    private float $ranks = 0.0;

    /**
     * @ORM\Column(type="float")
     */
    private float $withComputer = 0.0;

    /**
     * @ORM\Column(type="float")
     */
    private float $withFriend = 0.0;

    public function __construct()
    {
        $this->season = null;
    }

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

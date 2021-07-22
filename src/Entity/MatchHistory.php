<?php

namespace App\Entity;

use App\Repository\MatchHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatchHistoryRepository::class)
 */
class MatchHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="matchHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $userWin;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="matchHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $userLose;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateOfStart;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateOfEnd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $matchType;

    /**
     * @ORM\Column(type="json")
     */
    private $shotsHistory = [];

    /**
     * @ORM\ManyToOne(targetEntity=Season::class)
     */
    private $season;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserWin(): ?User
    {
        return $this->userWin;
    }

    public function setUserWin(?User $userWin): self
    {
        $this->userWin = $userWin;

        return $this;
    }

    public function getUserLose(): ?User
    {
        return $this->userLose;
    }

    public function setUserLose(?User $userLose): self
    {
        $this->userLose = $userLose;

        return $this;
    }

    public function getDateOfStart(): ?\DateTimeInterface
    {
        return $this->dateOfStart;
    }

    public function setDateOfStart(\DateTimeInterface $dateOfStart): self
    {
        $this->dateOfStart = $dateOfStart;

        return $this;
    }

    public function getDateOfEnd(): ?\DateTimeInterface
    {
        return $this->dateOfEnd;
    }

    public function setDateOfEnd(\DateTimeInterface $dateOfEnd): self
    {
        $this->dateOfEnd = $dateOfEnd;

        return $this;
    }

    public function getMatchType(): ?string
    {
        return $this->matchType;
    }

    public function setMatchType(string $matchType): self
    {
        $this->matchType = $matchType;

        return $this;
    }

    public function getShotsHistory(): ?array
    {
        return $this->shotsHistory;
    }

    public function setShotsHistory(array $shotsHistory): self
    {
        $this->shotsHistory = $shotsHistory;

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
}

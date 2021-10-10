<?php

namespace App\Entity;

use App\Repository\MatchmakingStorageRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=MatchmakingStorageRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class MatchmakingStorage
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $kindOfGame;

    /**
     * @ORM\Column(type="json")
     */
    private array $ships = [];

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return array
     */
    public function getShips(): array
    {
        return $this->ships;
    }

    /**
     * @param array $ships
     */
    public function setShips(array $ships): void
    {
        $this->ships = $ships;
    }

    public function getKindOfGame(): int
    {
        return $this->kindOfGame;
    }

    public function setKindOfGame(int $kindOfGame): void
    {
        $this->kindOfGame = $kindOfGame;
    }
}

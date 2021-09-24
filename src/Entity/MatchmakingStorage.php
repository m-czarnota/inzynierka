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
     * @ORM\Column(type="json")
     */
    private array $userGameInfo = [];

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

    public function getUserGameInfo(): array
    {
        return $this->userGameInfo;
    }

    public function setUserGameInfo(array $userGameInfo): void
    {
        $this->userGameInfo = $userGameInfo;
    }
}

<?php

namespace App\Entity;

use App\Entity\Enums\GameStateEnum;
use App\Entity\Enums\KindOfGameEnum;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Game
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=GameRoom::class, inversedBy="game", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameRoom;

    /**
     * @ORM\Column(type="json")
     */
    private $users = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private int $kindOfGame;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private int $gameState;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private int $playerTurn;

    /**
     * @ORM\Column(type="json")
     */
    private $gameInfo = [];

    public function __construct()
    {
        $this->gameState = GameStateEnum::CREATED;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameRoom(): ?GameRoom
    {
        return $this->gameRoom;
    }

    public function setGameRoom(GameRoom $gameRoom): self
    {
        $this->gameRoom = $gameRoom;

        return $this;
    }

    public function getUsers(): ?array
    {
        return $this->users;
    }

    public function setUsers(array $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getKindOfGame(): int
    {
        return $this->kindOfGame;
    }

    public function setKindOfGame(int $kindOfGame): self
    {
        $this->kindOfGame = $kindOfGame;

        return $this;
    }

    public function getGameState(): int
    {
        return $this->gameState;
    }

    public function setGameState(int $gameState): self
    {
        $this->gameState = $gameState;

        return $this;
    }

    public function getGameInfo(): ?array
    {
        return $this->gameInfo;
    }

    public function setGameInfo(array $gameInfo): self
    {
        $this->gameInfo = $gameInfo;

        return $this;
    }

    /**
     * @return int
     */
    public function getPlayerTurn(): int
    {
        return $this->playerTurn;
    }

    /**
     * @param int $playerTurn
     */
    public function setPlayerTurn(int $playerTurn): void
    {
        $this->playerTurn = $playerTurn;
    }


}

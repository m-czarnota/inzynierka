<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nick;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBlocked;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBanned;

    /**
     * @ORM\ManyToMany(targetEntity=Player::class)
     */
    private $friends;

    /**
     * @ORM\OneToOne(targetEntity=Score::class, mappedBy="player", cascade={"persist", "remove"})
     */
    private $score;

    /**
     * @ORM\OneToMany(targetEntity=WinnedCombination::class, mappedBy="player", orphanRemoval=true)
     */
    private $winnedCombinations;

    /**
     * @ORM\ManyToOne(targetEntity=GameRoom::class)
     */
    private $gameRoom;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class)
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity=MatchHistory::class, mappedBy="playerWin", orphanRemoval=true)
     */
    private $matchHistories;

    public function __construct()
    {
        $this->friends = new ArrayCollection();
        $this->winnedCombinations = new ArrayCollection();
        $this->matchHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        $this->friends->removeElement($friend);

        return $this;
    }

    public function getScore(): ?Score
    {
        return $this->score;
    }

    public function setScore(?Score $score): self
    {
        // unset the owning side of the relation if necessary
        if ($score === null && $this->score !== null) {
            $this->score->setPlayer(null);
        }

        // set the owning side of the relation if necessary
        if ($score !== null && $score->getPlayer() !== $this) {
            $score->setPlayer($this);
        }

        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection|WinnedCombination[]
     */
    public function getWinnedCombinations(): Collection
    {
        return $this->winnedCombinations;
    }

    public function addWinnedCombination(WinnedCombination $winnedCombination): self
    {
        if (!$this->winnedCombinations->contains($winnedCombination)) {
            $this->winnedCombinations[] = $winnedCombination;
            $winnedCombination->setPlayer($this);
        }

        return $this;
    }

    public function removeWinnedCombination(WinnedCombination $winnedCombination): self
    {
        if ($this->winnedCombinations->removeElement($winnedCombination)) {
            // set the owning side to null (unless already changed)
            if ($winnedCombination->getPlayer() === $this) {
                $winnedCombination->setPlayer(null);
            }
        }

        return $this;
    }

    public function getGameRoom(): ?GameRoom
    {
        return $this->gameRoom;
    }

    public function setGameRoom(?GameRoom $gameRoom): self
    {
        $this->gameRoom = $gameRoom;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection|MatchHistory[]
     */
    public function getMatchHistories(): Collection
    {
        return $this->matchHistories;
    }

    public function addMatchHistory(MatchHistory $matchHistory): self
    {
        if (!$this->matchHistories->contains($matchHistory)) {
            $this->matchHistories[] = $matchHistory;
            $matchHistory->setPlayerWin($this);
        }

        return $this;
    }

    public function removeMatchHistory(MatchHistory $matchHistory): self
    {
        if ($this->matchHistories->removeElement($matchHistory)) {
            // set the owning side to null (unless already changed)
            if ($matchHistory->getPlayerWin() === $this) {
                $matchHistory->setPlayerWin(null);
            }
        }

        return $this;
    }
}

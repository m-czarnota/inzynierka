<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private ?string $nick;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isBlocked = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isBanned = false;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     */
    private Collection $friends;

    /**
     * @ORM\OneToOne(targetEntity=Score::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private ?Score $score;

    /**
     * @ORM\OneToMany(targetEntity=WinnedCombination::class, mappedBy="user", orphanRemoval=true)
     */
    private Collection $winnedCombinations;

    /**
     * @ORM\OneToMany(targetEntity=MatchHistory::class, mappedBy="userWin", orphanRemoval=true)
     */
    private Collection $matchHistories;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="users")
     */
    private ?Game $game;

    /**
     * @ORM\ManyToOne(targetEntity=GameRoom::class, inversedBy="users")
     */
    private $gameRoom;

    public function __construct()
    {
        $this->game = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getEmail(): ?string
    {
        return (string)$this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
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
            $this->score->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($score !== null && $score->getUser() !== $this) {
            $score->setUser($this);
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
            $winnedCombination->setUser($this);
        }

        return $this;
    }

    public function removeWinnedCombination(WinnedCombination $winnedCombination): self
    {
        if ($this->winnedCombinations->removeElement($winnedCombination)) {
            // set the owning side to null (unless already changed)
            if ($winnedCombination->getUser() === $this) {
                $winnedCombination->setUser(null);
            }
        }

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
            $matchHistory->setUserWin($this);
        }

        return $this;
    }

    public function removeMatchHistory(MatchHistory $matchHistory): self
    {
        if ($this->matchHistories->removeElement($matchHistory)) {
            // set the owning side to null (unless already changed)
            if ($matchHistory->getUserWin() === $this) {
                $matchHistory->setUserWin(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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

    public function getGameRoom(): ?GameRoom
    {
        return $this->gameRoom;
    }

    public function setGameRoom(?GameRoom $gameRoom): self
    {
        $this->gameRoom = $gameRoom;

        return $this;
    }
}

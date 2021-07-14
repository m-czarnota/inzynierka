<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeasonRepository::class)
 */
class Season
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateOfStart;

    /**
     * @ORM\Column(type="date")
     */
    private $dateOfEnd;

    public function getId(): ?int
    {
        return $this->id;
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
}

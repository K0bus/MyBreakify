<?php

namespace App\Entity;

use App\Repository\UserBreakRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserBreakRepository::class)
 */
class UserBreak
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userBreaks")
     */
    private $user_id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="datetime")
     */
    private $requested_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getRequestedAt(): ?\DateTimeInterface
    {
        return $this->requested_at;
    }

    public function setRequestedAt(\DateTimeInterface $requested_at): self
    {
        $this->requested_at = $requested_at;

        return $this;
    }
}

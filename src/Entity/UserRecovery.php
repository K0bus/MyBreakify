<?php

namespace App\Entity;

use App\Repository\UserRecoveryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRecoveryRepository::class)
 */
class UserRecovery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userRecoveries")
     */
    private $user_id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $time_from;

    /**
     * @ORM\Column(type="time")
     */
    private $time_to;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $request_at;

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

    public function getTimeFrom(): ?\DateTimeInterface
    {
        return $this->time_from;
    }

    public function setTimeFrom(\DateTimeInterface $time_from): self
    {
        $this->time_from = $time_from;

        return $this;
    }

    public function getTimeTo(): ?\DateTimeInterface
    {
        return $this->time_to;
    }

    public function setTimeTo(\DateTimeInterface $time_to): self
    {
        $this->time_to = $time_to;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRequestAt(): ?\DateTimeInterface
    {
        return $this->request_at;
    }

    public function setRequestAt(\DateTimeInterface $request_at): self
    {
        $this->request_at = $request_at;

        return $this;
    }
}

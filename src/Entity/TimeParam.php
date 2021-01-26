<?php

namespace App\Entity;

use App\Repository\TimeParamRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimeParamRepository::class)
 */
class TimeParam
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
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="integer")
     */
    private $break;

    /**
     * @ORM\Column(type="integer")
     */
    private $recovery;

    /**
     * @ORM\Column(type="integer")
     */
    private $break_adm;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBreak(): ?int
    {
        return $this->break;
    }

    public function setBreak(int $break): self
    {
        $this->break = $break;

        return $this;
    }

    public function getRecovery(): ?int
    {
        return $this->recovery;
    }

    public function setRecovery(int $recovery): self
    {
        $this->recovery = $recovery;

        return $this;
    }

    public function getBreakAdm(): ?int
    {
        return $this->break_adm;
    }

    public function setBreakAdm(int $break_adm): self
    {
        $this->break_adm = $break_adm;

        return $this;
    }
}

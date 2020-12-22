<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $logged_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="users")
     */
    private $n1;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="n1")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=UserBreak::class, mappedBy="user_id")
     */
    private $userBreaks;

    /**
     * @ORM\OneToMany(targetEntity=UserRecovery::class, mappedBy="user_id")
     */
    private $userRecoveries;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->userBreaks = new ArrayCollection();
        $this->userRecoveries = new ArrayCollection();
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
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getLoggedAt(): ?\DateTimeInterface
    {
        return $this->logged_at;
    }

    public function setLoggedAt(\DateTimeInterface $logged_at): self
    {
        $this->logged_at = $logged_at;

        return $this;
    }

    public function getN1(): ?self
    {
        return $this->n1;
    }

    public function setN1(?self $n1): self
    {
        $this->n1 = $n1;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setN1($this);
        }

        return $this;
    }

    public function removeUser(self $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getN1() === $this) {
                $user->setN1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserBreak[]
     */
    public function getUserBreaks(): Collection
    {
        return $this->userBreaks;
    }

    public function addUserBreak(UserBreak $userBreak): self
    {
        if (!$this->userBreaks->contains($userBreak)) {
            $this->userBreaks[] = $userBreak;
            $userBreak->setUserId($this);
        }

        return $this;
    }

    public function removeUserBreak(UserBreak $userBreak): self
    {
        if ($this->userBreaks->removeElement($userBreak)) {
            // set the owning side to null (unless already changed)
            if ($userBreak->getUserId() === $this) {
                $userBreak->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserRecovery[]
     */
    public function getUserRecoveries(): Collection
    {
        return $this->userRecoveries;
    }

    public function addUserRecovery(UserRecovery $userRecovery): self
    {
        if (!$this->userRecoveries->contains($userRecovery)) {
            $this->userRecoveries[] = $userRecovery;
            $userRecovery->setUserId($this);
        }

        return $this;
    }

    public function removeUserRecovery(UserRecovery $userRecovery): self
    {
        if ($this->userRecoveries->removeElement($userRecovery)) {
            // set the owning side to null (unless already changed)
            if ($userRecovery->getUserId() === $this) {
                $userRecovery->setUserId(null);
            }
        }

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}

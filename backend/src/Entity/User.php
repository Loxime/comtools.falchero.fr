<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'uniq_users_email', columns: ['email'])]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower(trim($email));

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $username = $username !== null ? trim($username) : null;
        $this->username = $username !== '' ? $username : null;

        return $this;
    }

    public function getRoles(): array
    {
        return array_values(array_unique([...$this->roles, 'ROLE_USER']));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'roles' => $this->getRoles(),
            'isActive' => $this->isActive,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}

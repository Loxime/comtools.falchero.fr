<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 160)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = trim($title);

        return $this;
    }

    public function setContent(?string $content): self
    {
        $content = $content !== null ? trim($content) : null;
        $this->content = $content !== '' ? $content : null;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}

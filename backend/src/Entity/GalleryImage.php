<?php

namespace App\Entity;

use App\Repository\GalleryImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryImageRepository::class)]
#[ORM\Table(name: 'gallery_images')]
class GalleryImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 255)]
    private string $url;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $alt = null;

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

    public function setUrl(string $url): self
    {
        $this->url = trim($url);

        return $this;
    }

    public function setAlt(?string $alt): self
    {
        $alt = $alt !== null ? trim($alt) : null;
        $this->alt = $alt !== '' ? $alt : null;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'alt' => $this->alt,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}

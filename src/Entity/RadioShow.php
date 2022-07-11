<?php

namespace App\Entity;

use App\Repository\RadioShowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RadioShowRepository::class)]
class RadioShow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 300)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $time;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'radioShows')]
    #[ORM\JoinColumn(nullable: false)]
    private $animator;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $podcastLink;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $logo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getAnimator(): ?User
    {
        return $this->animator;
    }

    public function setAnimator(?User $animator): self
    {
        $this->animator = $animator;

        return $this;
    }

    public function getPodcastLink(): ?string
    {
        return $this->podcastLink;
    }

    public function setPodcastLink(?string $podcastLink): self
    {
        $this->podcastLink = $podcastLink;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }
}

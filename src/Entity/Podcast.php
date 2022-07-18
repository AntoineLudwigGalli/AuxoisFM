<?php

namespace App\Entity;

use App\Repository\PodcastRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PodcastRepository::class)]
class Podcast
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 300)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $podcastLink = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $broadcastDate = null;

    #[ORM\ManyToOne(inversedBy: 'podcasts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RadioShow $radioShow = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $downloadLink = null;

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

    public function getPodcastLink(): ?string
    {
        return $this->podcastLink;
    }

    public function setPodcastLink(string $podcastLink): self
    {
        $this->podcastLink = $podcastLink;

        return $this;
    }

    public function getBroadcastDate(): ?\DateTimeInterface
    {
        return $this->broadcastDate;
    }

    public function setBroadcastDate(\DateTimeInterface $broadcastDate): self
    {
        $this->broadcastDate = $broadcastDate;

        return $this;
    }

    public function getRadioShow(): ?RadioShow
    {
        return $this->radioShow;
    }

    public function setRadioShow(?RadioShow $radioShow): self
    {
        $this->radioShow = $radioShow;

        return $this;
    }

    public function getDownloadLink(): ?string
    {
        return $this->downloadLink;
    }

    public function setDownloadLink(?string $downloadLink): self
    {
        $this->downloadLink = $downloadLink;

        return $this;
    }
}

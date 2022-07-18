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


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'radioShows')]
    #[ORM\JoinColumn(nullable: false)]
    private $animator;

    #[ORM\Column(type: 'string', length: 255)]
    private $webPageLink;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $logo;


    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtubeURL = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spotifyURL = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deezerURL = null;

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


    public function getAnimator(): ?User
    {
        return $this->animator;
    }

    public function setAnimator(?User $animator): self
    {
        $this->animator = $animator;

        return $this;
    }

    public function getwebPageLink(): ?string
    {
        return $this->webPageLink;
    }

    public function setwebPageLink(?string $webPageLink): self
    {
        $this->webPageLink = $webPageLink;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getYoutubeURL(): ?string
    {
        return $this->youtubeURL;
    }

    public function setYoutubeURL(?string $youtubeURL): self
    {
        $this->youtubeURL = $youtubeURL;

        return $this;
    }

    public function getSpotifyURL(): ?string
    {
        return $this->spotifyURL;
    }

    public function setSpotifyURL(?string $spotifyURL): self
    {
        $this->spotifyURL = $spotifyURL;

        return $this;
    }

    public function getDeezerURL(): ?string
    {
        return $this->deezerURL;
    }

    public function setDeezerURL(?string $deezerURL): self
    {
        $this->deezerURL = $deezerURL;

        return $this;
    }

}

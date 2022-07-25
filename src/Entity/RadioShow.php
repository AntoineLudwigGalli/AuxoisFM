<?php

namespace App\Entity;

use App\Repository\RadioShowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $youtubeURL = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $spotifyURL = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $deezerURL = null;

    #[ORM\OneToMany(mappedBy: 'radioShow', targetEntity: Podcast::class, orphanRemoval: true)]
    private Collection $podcasts;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;


    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $showTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $showDuration = null;


    #[ORM\Column]
    private ?int $timeInterval = null;

    #[ORM\Column]
    private array $broadcastDay = [];

    #[ORM\OneToMany(mappedBy: 'webpage', targetEntity: ShowWebpageOptions::class, orphanRemoval: true)]
    private Collection $showWebpageOptions;



    public function __construct()
    {
        $this->podcasts = new ArrayCollection();
        $this->showWebpageOptions = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Podcast>
     */
    public function getPodcasts(): Collection
    {
        return $this->podcasts;
    }

    public function addPodcast(Podcast $podcast): self
    {
        if (!$this->podcasts->contains($podcast)) {
            $this->podcasts[] = $podcast;
            $podcast->setRadioShow($this);
        }

        return $this;
    }

    public function removePodcast(Podcast $podcast): self
    {
        if ($this->podcasts->removeElement($podcast)) {
            // set the owning side to null (unless already changed)
            if ($podcast->getRadioShow() === $this) {
                $podcast->setRadioShow(null);
            }
        }

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }


    public function getShowTime(): ?\DateTimeInterface
    {
        return $this->showTime;
    }

    public function setShowTime(\DateTimeInterface $showTime): self
    {
        $this->showTime = $showTime;

        return $this;
    }

    public function getShowDuration(): ?\DateTimeInterface
    {
        return $this->showDuration;
    }

    public function setShowDuration(?\DateTimeInterface $showDuration): self
    {
        $this->showDuration = $showDuration;

        return $this;
    }



    public function getTimeInterval(): ?int
    {
        return $this->timeInterval;
    }

    public function setTimeInterval(int $timeInterval): self
    {
        $this->timeInterval = $timeInterval;

        return $this;
    }

    public function getBroadcastDay(): array
    {
        return $this->broadcastDay;
    }

    public function setBroadcastDay(array $broadcastDay): self
    {
        $this->broadcastDay = $broadcastDay;

        return $this;
    }

    /**
     * @return Collection<int, ShowWebpageOptions>
     */
    public function getShowWebpageOptions(): Collection
    {
        return $this->showWebpageOptions;
    }

    public function addShowWebpageOption(ShowWebpageOptions $showWebpageOption): self
    {
        if (!$this->showWebpageOptions->contains($showWebpageOption)) {
            $this->showWebpageOptions[] = $showWebpageOption;
            $showWebpageOption->setWebpage($this);
        }

        return $this;
    }

    public function removeShowWebpageOption(ShowWebpageOptions $showWebpageOption): self
    {
        if ($this->showWebpageOptions->removeElement($showWebpageOption)) {
            // set the owning side to null (unless already changed)
            if ($showWebpageOption->getWebpage() === $this) {
                $showWebpageOption->setWebpage(null);
            }
        }

        return $this;
    }



}

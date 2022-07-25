<?php

namespace App\Entity;

use App\Repository\ShowWebpageOptionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShowWebpageOptionsRepository::class)]
class ShowWebpageOptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $backgroundColor = "null";

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $textColor = "null";

    #[ORM\ManyToOne(inversedBy: 'showWebpageOptions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?RadioShow $webpage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getWebpage(): ?RadioShow
    {
        return $this->webpage;
    }

    public function setWebpage(?RadioShow $webpage): self
    {
        $this->webpage = $webpage;

        return $this;
    }
}

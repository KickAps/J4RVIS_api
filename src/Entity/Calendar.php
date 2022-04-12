<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'calendars')]
    #[ORM\JoinColumn(nullable: false)]
    private $activity;

    #[ORM\Column(type: 'datetime_immutable')]
    private $startedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $stoppedAt;

    public function getId(): ?int {
        return $this->id;
    }

    public function getActivity(): ?Activity {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self {
        $this->activity = $activity;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): self {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getStoppedAt(): ?\DateTimeImmutable {
        return $this->stoppedAt;
    }

    public function setStoppedAt(\DateTimeImmutable $stoppedAt): self {
        $this->stoppedAt = $stoppedAt;

        return $this;
    }
}

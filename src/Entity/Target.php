<?php

namespace App\Entity;

use App\Repository\TargetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TargetRepository::class)]
class Target {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'targets')]
    #[ORM\JoinColumn(nullable: false)]
    private $activity;

    #[ORM\Column(type: 'float')]
    private $hours;

    #[ORM\Column(type: 'string', length: 1)]
    private $period;

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

    public function getHours(): ?float {
        return $this->hours;
    }

    public function setHours(float $hours): self {
        $this->hours = $hours;

        return $this;
    }

    public function getPeriod(): ?string {
        return $this->period;
    }

    public function setPeriod(string $period): self {
        $this->period = $period;

        return $this;
    }
}

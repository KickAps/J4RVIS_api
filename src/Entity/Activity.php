<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Activity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $title;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Calendar::class)]
    private $calendars;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Target::class)]
    private $targets;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $color;

    public function __construct() {
        $this->calendars = new ArrayCollection();
        $this->targets = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): self {
        if(!$this->calendars->contains($calendar)) {
            $this->calendars[] = $calendar;
            $calendar->setActivity($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): self {
        if($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if($calendar->getActivity() === $this) {
                $calendar->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Target>
     */
    public function getTargets(): Collection {
        return $this->targets;
    }

    public function addTarget(Target $target): self {
        if(!$this->targets->contains($target)) {
            $this->targets[] = $target;
            $target->setActivity($this);
        }

        return $this;
    }

    public function removeTarget(Target $target): self {
        if($this->targets->removeElement($target)) {
            // set the owning side to null (unless already changed)
            if($target->getActivity() === $this) {
                $target->setActivity(null);
            }
        }

        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): self {
        $this->color = $color;
        return $this;
    }

    #[ORM\PrePersist]
    public function setDefaultColor(): self {
        $this->color = "#3498db";
        return $this;
    }
}

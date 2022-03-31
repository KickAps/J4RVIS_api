<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $title;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Calendar::class)]
    private $calendars;

    public function __construct() {
        $this->calendars = new ArrayCollection();
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
}

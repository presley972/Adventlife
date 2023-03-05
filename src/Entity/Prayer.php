<?php

namespace App\Entity;

use App\Repository\PrayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrayerRepository::class)]
class Prayer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $modifiedAt;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'prayers')]
    private $owner;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'prayers')]
    private $groupe;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $security;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'prayers')]
    private $place;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'membership_prayers')]
    private $membership;

    public function __construct()
    {
        $this->membership = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeImmutable $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getGroupe(): ?Group
    {
        return $this->groupe;
    }

    public function setGroupe(?Group $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getSecurity(): ?bool
    {
        return $this->security;
    }

    public function setSecurity(?bool $security): self
    {
        $this->security = $security;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembership(): Collection
    {
        return $this->membership;
    }

    public function addMembership(User $membership): self
    {
        if (!$this->membership->contains($membership)) {
            $this->membership[] = $membership;
        }

        return $this;
    }

    public function removeMembership(User $membership): self
    {
        $this->membership->removeElement($membership);

        return $this;
    }
}

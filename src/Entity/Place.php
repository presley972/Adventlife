<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $modifiedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $Adress;

    #[ORM\Column(type: 'float', nullable: true)]
    private $lat;

    #[ORM\Column(type: 'float', nullable: true)]
    private $lng;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $country;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $zipCode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $locality;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $areaRegion;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $areaDepartement;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $street;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $streetNumber;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $placeId;

    #[ORM\OneToMany(mappedBy: 'place', targetEntity: Group::class)]
    private $homeGroup;

    public function __construct()
    {
        $this->homeGroup = new ArrayCollection();
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

    public function getAdress(): ?string
    {
        return $this->Adress;
    }

    public function setAdress(string $Adress): self
    {
        $this->Adress = $Adress;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getAreaRegion(): ?string
    {
        return $this->areaRegion;
    }

    public function setAreaRegion(?string $areaRegion): self
    {
        $this->areaRegion = $areaRegion;

        return $this;
    }

    public function getAreaDepartement(): ?string
    {
        return $this->areaDepartement;
    }

    public function setAreaDepartement(?string $areaDepartement): self
    {
        $this->areaDepartement = $areaDepartement;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?string $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function setPlaceId(?string $placeId): self
    {
        $this->placeId = $placeId;

        return $this;
    }


    /**
     * @return Collection|Group[]
     */
    public function getHomeGroup(): Collection
    {
        return $this->homeGroup;
    }

    public function addHomeGroup(Group $homeGroup): self
    {
        if (!$this->homeGroup->contains($homeGroup)) {
            $this->homeGroup[] = $homeGroup;
            $homeGroup->setPlace($this);
        }

        return $this;
    }

    public function removeHomeGroup(Group $homeGroup): self
    {
        if ($this->homeGroup->removeElement($homeGroup)) {
            // set the owning side to null (unless already changed)
            if ($homeGroup->getPlace() === $this) {
                $homeGroup->setPlace(null);
            }
        }

        return $this;
    }
}

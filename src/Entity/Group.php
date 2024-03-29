<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    const LOCATIONREUNION = ['Presentiel' => 'Presentiel', 'Distantiel' => 'Distantiel'];
    const THEME = ['Prière'=> 'Prière', 'Louange'=> 'Louange', 'Partage'=>'Partage'];
    const FREQUENCE = ['Hebdomadaire'=>'Hebdomadaire', 'Mensuel'=>'Mensuel', 'Journalier'=>'Journalier'];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownerGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups')]
    private $members;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Subscription::class, orphanRemoval: true)]
    private $subscriptions;

    #[ORM\OneToOne(mappedBy: 'groupe', targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private $image;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: BlogPost::class)]
    private $blogPosts;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'homeGroup')]
    private $place;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Evenement::class)]
    private $evenements;

    #[ORM\ManyToMany(targetEntity: GroupCategory::class, mappedBy: 'groupe')]
    private $groupCategories;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $theme;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $frequence;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $location;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Prayer::class)]
    private $prayers;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->blogPosts = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->groupCategories = new ArrayCollection();
        $this->prayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setGroupe($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getGroupe() === $this) {
                $subscription->setGroupe(null);
            }
        }

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        // unset the owning side of the relation if necessary
        if ($image === null && $this->image !== null) {
            $this->image->setGroupe(null);
        }

        // set the owning side of the relation if necessary
        if ($image !== null && $image->getGroupe() !== $this) {
            $image->setGroupe($this);
        }

        $this->image = $image;

        return $this;
    }
    public function __toString(): string
    {

        return $this->getTitle();
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->setGroupe($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->removeElement($blogPost)) {
            // set the owning side to null (unless already changed)
            if ($blogPost->getGroupe() === $this) {
                $blogPost->setGroupe(null);
            }
        }

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
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setGroupe($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getGroupe() === $this) {
                $evenement->setGroupe(null);
            }
        }

        return $this;
    }

    public function checkIfUserIsSubcriber(User $user)
    {
        foreach ($this->getSubscriptions() as $subscription){
            if ($subscription->getSubscriber() === $user){
                return true;
            }
        }
        return false;
    }
    public function checkIfUserIsMember(User $user)
    {
        if ($this->getOwner()->getId() === $user->getId()){
            return true;
        }
        foreach ($this->getSubscriptions() as $subscription){
            if ($subscription->getSubscriber() === $user && $subscription->getGroupStatut() === Subscription::ACCEPTED){
                return true;
            }
        }
        return false;
    }

    public function checkIfUserIsBlocked($user)
    {
        foreach ($this->getSubscriptions() as $subscription){
            if ($subscription->getGroupStatut() === Subscription::BLOCKED && $subscription->getSubscriber()->getId() === $user){
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection|GroupCategory[]
     */
    public function getGroupCategories(): Collection
    {
        return $this->groupCategories;
    }

    public function addGroupCategory(GroupCategory $groupCategory): self
    {
        if (!$this->groupCategories->contains($groupCategory)) {
            $this->groupCategories[] = $groupCategory;
            $groupCategory->addGroupe($this);
        }

        return $this;
    }

    public function removeGroupCategory(GroupCategory $groupCategory): self
    {
        if ($this->groupCategories->removeElement($groupCategory)) {
            $groupCategory->removeGroupe($this);
        }

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getFrequence(): ?string
    {
        return $this->frequence;
    }

    public function setFrequence(?string $frequence): self
    {
        $this->frequence = $frequence;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Prayer[]
     */
    public function getPrayers(): Collection
    {
        return $this->prayers;
    }

    public function addPrayer(Prayer $prayer): self
    {
        if (!$this->prayers->contains($prayer)) {
            $this->prayers[] = $prayer;
            $prayer->setGroupe($this);
        }

        return $this;
    }

    public function removePrayer(Prayer $prayer): self
    {
        if ($this->prayers->removeElement($prayer)) {
            // set the owning side to null (unless already changed)
            if ($prayer->getGroupe() === $this) {
                $prayer->setGroupe(null);
            }
        }

        return $this;
    }

}

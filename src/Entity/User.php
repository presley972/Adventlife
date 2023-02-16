<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Group::class)]
    private $ownerGroups;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'members')]
    private $groups;

    #[ORM\OneToMany(mappedBy: 'subscriber', targetEntity: Subscription::class, orphanRemoval: true)]
    private $subscriptions;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: BlogPost::class)]
    private $blogPosts;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Comment::class)]
    private $comments;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Evenement::class)]
    private $evenements;

    #[ORM\ManyToMany(targetEntity: Evenement::class, mappedBy: 'subscriber')]
    private $subscriber_evenements;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phoneNumber;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $church;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $privacyPolicy;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $showEmail;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $showPhoneNumber;
    #[ORM\OneToOne(mappedBy: 'userProfilPicture', targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private $profilPicture;

    public function __construct()
    {
        $this->ownerGroups = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->blogPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->subscriber_evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getOwnerGroups(): Collection
    {
        return $this->ownerGroups;
    }

    public function addOwnerGroup(Group $ownerGroup): self
    {
        if (!$this->ownerGroups->contains($ownerGroup)) {
            $this->ownerGroups[] = $ownerGroup;
            $ownerGroup->setOwner($this);
        }

        return $this;
    }

    public function removeOwnerGroup(Group $ownerGroup): self
    {
        if ($this->ownerGroups->removeElement($ownerGroup)) {
            // set the owning side to null (unless already changed)
            if ($ownerGroup->getOwner() === $this) {
                $ownerGroup->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addMember($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            $group->removeMember($this);
        }

        return $this;
    }

    public function __toString(){
        return $this->getEmail();
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
            $subscription->setSubscriber($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getSubscriber() === $this) {
                $subscription->setSubscriber(null);
            }
        }

        return $this;
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
            $blogPost->setOwner($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->removeElement($blogPost)) {
            // set the owning side to null (unless already changed)
            if ($blogPost->getOwner() === $this) {
                $blogPost->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setOwner($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getOwner() === $this) {
                $comment->setOwner(null);
            }
        }

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
            $evenement->setCreatedBy($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getCreatedBy() === $this) {
                $evenement->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getSubscriberEvenements(): Collection
    {
        return $this->subscriber_evenements;
    }

    public function addSubscriberEvenement(Evenement $subscriberEvenement): self
    {
        if (!$this->subscriber_evenements->contains($subscriberEvenement)) {
            $this->subscriber_evenements[] = $subscriberEvenement;
            $subscriberEvenement->addSubscriber($this);
        }

        return $this;
    }

    public function removeSubscriberEvenement(Evenement $subscriberEvenement): self
    {
        if ($this->subscriber_evenements->removeElement($subscriberEvenement)) {
            $subscriberEvenement->removeSubscriber($this);
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getChurch(): ?string
    {
        return $this->church;
    }

    public function setChurch(?string $church): self
    {
        $this->church = $church;

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

    public function getPrivacyPolicy(): ?bool
    {
        return $this->privacyPolicy;
    }

    public function setPrivacyPolicy(?bool $privacyPolicy): self
    {
        $this->privacyPolicy = $privacyPolicy;

        return $this;
    }

    public function getShowEmail(): ?bool
    {
        return $this->showEmail;
    }

    public function setShowEmail(?bool $showEmail): self
    {
        $this->showEmail = $showEmail;

        return $this;
    }

    public function getShowPhoneNumber(): ?bool
    {
        return $this->showPhoneNumber;
    }

    public function setShowPhoneNumber(?bool $showPhoneNumber): self
    {
        $this->showPhoneNumber = $showPhoneNumber;

        return $this;
    }

    public function getProfilPicture(): ?Image
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(?Image $image): self
    {
        // unset the owning side of the relation if necessary
        if ($image === null && $this->profilPicture !== null) {
            $this->profilPicture->setUserProfilPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($image !== null && $image->getUserProfilPicture() !== $this) {
            $image->setUserProfilPicture($this);
        }

        $this->profilPicture = $image;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    const SEND = 'SEND_INVITATION';
    const WAIT = 'WAITING_ACCEPTANCE';
    const ACCEPTED = 'ACCEPTED';
    const DENIED = 'DENIED';
    const BLOCKED = 'BLOCKED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private $groupe;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private $subscriber;

    #[ORM\Column(type: 'string', length: 255)]
    private $group_statut;

    #[ORM\Column(type: 'string', length: 255)]
    private $subscriber_statut;

    #[ORM\Column(type: 'boolean')]
    private $notification_checked;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $group_acceptedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $subscriber_acceptedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubscriber(): ?User
    {
        return $this->subscriber;
    }

    public function setSubscriber(?User $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function getGroupStatut(): ?string
    {
        return $this->group_statut;
    }

    public function setGroupStatut(string $group_statut): self
    {
        $this->group_statut = $group_statut;

        return $this;
    }

    public function getSubscriberStatut(): ?string
    {
        return $this->subscriber_statut;
    }

    public function setSubscriberStatut(string $subscriber_statut): self
    {
        $this->subscriber_statut = $subscriber_statut;

        return $this;
    }

    public function getNotificationChecked(): ?bool
    {
        return $this->notification_checked;
    }

    public function setNotificationChecked(bool $notification_checked): self
    {
        $this->notification_checked = $notification_checked;

        return $this;
    }

    public function getGroupAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->group_acceptedAt;
    }

    public function setGroupAcceptedAt(?\DateTimeImmutable $group_acceptedAt): self
    {
        $this->group_acceptedAt = $group_acceptedAt;

        return $this;
    }

    public function getSubscriberAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->subscriber_acceptedAt;
    }

    public function setSubscriberAcceptedAt(?\DateTimeImmutable $subscriber_acceptedAt): self
    {
        $this->subscriber_acceptedAt = $subscriber_acceptedAt;

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

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeImmutable $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getGroupStatut();
    }
}

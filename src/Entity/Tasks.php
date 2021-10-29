<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TasksRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Tasks
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Title is required", allowNull = false)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="integer", options={"default": 1})
     * @Assert\NotBlank(message = "Priority is required", allowNull = false)
     */
    private $priority = 1;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message = "Run at is required", allowNull = false)
     * @Assert\DateTime
     */
    private $run_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lists", inversedBy="tasks", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message = "List is required", allowNull = false)
     */
    private $list;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $isDone = 0;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $isNotified = 0;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAt(): self
    {
        // to save the time in the local (Africa/Cairo) timezone in the DB
        // $this->created_at = new \DateTime('now', new \DateTimeZone('Africa/Cairo'));

        // to the save the time in the UTC time in the DB.
        $this->created_at = new \DateTime();

        return $this;
    }

    public function getRunAt(): ?\DateTimeInterface
    {
        return $this->run_at;
    }

    public function setRunAt(string $RunAt): self
    {
        $this->run_at = new \DateTime($RunAt);

        return $this;
    }

    public function getList(): ?Lists
    {
        return $this->list;
    }

    public function setList(?Lists $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsDone(): bool
    {
        return $this->isDone;
    }

    public function setIsDone(?bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function getIsNotified(): ?bool
    {
        return $this->isNotified;
    }

    public function setIsNotified(?bool $isNotified): self
    {
        $this->isNotified = $isNotified;

        return $this;
    }
}

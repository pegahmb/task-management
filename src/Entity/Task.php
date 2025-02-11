<?php

namespace App\Entity;

use App\Dto\TaskDto;
use App\Enum\StatusType;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task extends AbstractEntity
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Title is required.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Title must be at least {{ limit }} characters.")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 500, maxMessage: "Description must not exceed {{ limit }} characters.")]
    #[Groups(["task:read", "task:write"])]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\Choice(callback: [StatusType::class, 'getValues'], message: "Invalid status.")]
    #[Groups(["task:read", "task:write"])]
    private ?string $status = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(["task:read", "task:write"])]
    private ?int $priority = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type(DateTime::class, message: "Invalid date format.")]
    #[Assert\GreaterThan("today", message: "Due date must be in the future.")]
    #[Groups(["task:read", "task:write"])]
    private ?DateTime $dueDate = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    private ?User $assignee = null;


    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'tasks')]
    private ?Task $parent = null;

    //-------------------------------------------------------------------

    public function __construct()
    {
    }

    //-------------------------------------------------------------------

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate?->format('Y-m-d H:i:s');
    }

    public function setDueDate(?string $dueDate): void
    {
        $this->dueDate =$dueDate?new DateTime($dueDate):null;
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee): void
    {
        $this->assignee = $assignee;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    public function setParent(?Task $parent): void
    {
        $this->parent = $parent;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }
    //------------------------------------------------------------------------

    public function dtoEntity(): TaskDto
    {
        return new TaskDto(
            $this->getId(),
            $this->getTitle(),
            $this->getDescription(),
            $this->getStatus(),
            $this->getPriority(),
            $this->getDueDate(),
            $this->getParent()?->getId(),
            $this->getAssignee()?->getId(),
            $this->getAssignee()?->getFullName(),
            $this->getCreatedAt()
        );
    }
}

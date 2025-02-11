<?php

namespace App\Dto;

use App\Enum\StatusType;

class TaskDto
{

    public ?int $id = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $status = null;
    public ?int $priority = null;
    public ?string $dueDate = null;
    public ?int $parentId = null;
    public ?int $assigneeId = null;
    public ?int $assigneeFullName = null;
    public ?string $createdAt = null;
    //-------------------------------------------------------------------

    /**
     * @param int|null $id
     * @param string|null $title
     * @param string|null $description
     * @param string|null $status
     * @param int|null $priority
     * @param string|null $dueDate
     * @param int|null $parentId
     * @param int|null $assigneeId
     * @param int|null $assigneeFullName
     * @param string|null $createdAt
     */
    public function __construct(?int $id, ?string $title, ?string $description, ?string $status, ?int $priority, ?string $dueDate, ?int $parentId, ?int $assigneeId, ?int $assigneeFullName, ?string $createdAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->priority = $priority;
        $this->dueDate = $dueDate;
        $this->parentId = $parentId;
        $this->assigneeId = $assigneeId;
        $this->assigneeFullName = $assigneeFullName;
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $data): ?self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['status'] ?? null,
            $data['priority'] ?? null,
            $data['dueDate'] ?? null,
            $data['parentId'] ?? null,
            $data['assigneeId'] ?? null,
            null, null
        );
    }

}

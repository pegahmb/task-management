<?php

namespace App\Dto;

use App\Enum\StatusType;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;

class TaskFilterDto
{
    public ?string $status = null;

    #[Assert\Date]
    public ?string $createdFrom = null;

    #[Assert\Date]
    public ?string $createdTo = null;

    #[Assert\Type(type: Types::INTEGER, message: "assigneeId must be a integer.")]
    public ?int $assigneeId = null;

    //------------------------------------------------------------------------
    public function __construct(?string $status, ?string $createdFrom, ?string $createdTo, ?string $assigneeId)
    {
        $this->status = $status;
        $this->createdFrom = $createdFrom;
        $this->createdTo = $createdTo;
        $this->assigneeId = $assigneeId;
    }

    //------------------------------------------------------------------------

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->query->get('status'),
            $request->query->get('createdFrom'),
            $request->query->get('createdTo'),
            $request->query->get('assigneeId')
        );
    }
}

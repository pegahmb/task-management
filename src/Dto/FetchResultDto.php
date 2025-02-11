<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;

class FetchResultDto
{

    public ?int $page;
    public ?int $limit;
    public ?int $total;
    public ?array $result;

    //------------------------------------------------------------------------

    /**
     * @param int|null $page
     * @param int|null $limit
     */
    public function __construct(?int $page, ?int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function setResult(?array $result): void
    {
        $this->result = $result;
    }

    //------------------------------------------------------------------------

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->query->get('page', 1),
            $request->query->get('limit', 10)
        );
    }
}

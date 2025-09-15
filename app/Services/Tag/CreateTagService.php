<?php

namespace App\Services\Tag;

use App\Dtos\CreateTagDto;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;

class CreateTagService
{
    public function __construct(private readonly TagRepositoryInterface $repository)
    {
    }

    public function execute(CreateTagDto $dto): Tag
    {
        return $this->repository->create($dto);
    }
}

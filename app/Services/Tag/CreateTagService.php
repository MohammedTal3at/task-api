<?php

namespace App\Services\Tag;

use App\Dtos\CreateTagDto;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;

readonly class CreateTagService
{
    public function __construct(private TagRepositoryInterface $repository)
    {
    }

    public function execute(CreateTagDto $dto): Tag
    {
        return $this->repository->create($dto);
    }
}

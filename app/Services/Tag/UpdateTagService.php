<?php

namespace App\Services\Tag;

use App\Dtos\UpdateTagDto;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;

class UpdateTagService
{
    public function __construct(private readonly TagRepositoryInterface $repository)
    {
    }

    public function execute(int $id, UpdateTagDto $dto): Tag
    {
        return $this->repository->update($id, $dto);
    }
}

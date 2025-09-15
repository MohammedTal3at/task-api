<?php

namespace App\Services\Tag;

use App\Repositories\Contracts\TagRepositoryInterface;

class DeleteTagService
{
    public function __construct(private readonly TagRepositoryInterface $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->delete($id);
    }
}

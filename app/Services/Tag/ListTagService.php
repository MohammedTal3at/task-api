<?php

namespace App\Services\Tag;

use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class ListTagService
{
    public function __construct(private TagRepositoryInterface $repository)
    {
    }

    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($perPage);
    }
}

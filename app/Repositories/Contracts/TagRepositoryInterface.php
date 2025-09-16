<?php

namespace App\Repositories\Contracts;

use App\Dtos\CreateTagDto;
use App\Dtos\UpdateTagDto;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagRepositoryInterface
{
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    public function create(CreateTagDto $dto): Tag;

    public function update(int $id, UpdateTagDto $dto): Tag;

    public function delete(int $id): int;
}

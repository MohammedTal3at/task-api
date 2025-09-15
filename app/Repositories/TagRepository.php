<?php

namespace App\Repositories;

use App\Dtos\CreateTagDto;
use App\Dtos\UpdateTagDto;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagRepository implements TagRepositoryInterface
{
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Tag::paginate($perPage);
    }

    public function create(CreateTagDto $dto): Tag
    {
        return Tag::create((array) $dto);
    }

    public function update(int $id, UpdateTagDto $dto): Tag
    {
        $tag = Tag::findOrFail($id);
        $tag->update(array_filter((array) $dto));

        return $tag;
    }

    public function delete(int $id): void
    {
        Tag::findOrFail($id)->delete();
    }
}

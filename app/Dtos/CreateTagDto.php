<?php

namespace App\Dtos;

use App\Http\Requests\Tag\CreateTagRequest;

readonly class CreateTagDto
{
    public function __construct(
        public string $name,
        public ?string $color = null
    ) {
    }

    public static function createFromRequest(CreateTagRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            color: $data['color'] ?? null
        );
    }
}

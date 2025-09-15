<?php

namespace App\Dtos;

use App\Http\Requests\Tag\UpdateTagRequest;

readonly class UpdateTagDto
{
    public function __construct(
        public ?string $name = null,
        public ?string $color = null
    ) {
    }

    public static function createFromRequest(UpdateTagRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'] ?? null,
            color: $data['color'] ?? null
        );
    }
}

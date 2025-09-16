<?php

namespace App\Http\Controllers\Api;

use App\Dtos\CreateTagDto;
use App\Dtos\UpdateTagDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Services\Tag\CreateTagService;
use App\Services\Tag\DeleteTagService;
use App\Services\Tag\ListTagService;
use App\Services\Tag\UpdateTagService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(ListTagService $service): AnonymousResourceCollection
    {
        return TagResource::collection($service->execute());
    }

    public function store(CreateTagRequest $request, CreateTagService $service): TagResource
    {
        $dto = CreateTagDto::createFromRequest($request);
        $tag = $service->execute($dto);

        return new TagResource($tag);
    }

    public function update(UpdateTagRequest $request, Tag $tag, UpdateTagService $service): TagResource
    {
        $dto = UpdateTagDto::createFromRequest($request);
        $tag = $service->execute($tag->id, $dto);

        return new TagResource($tag);
    }

    public function destroy(int $tagId, DeleteTagService $service): Response
    {
        $service->execute($tagId);

        return response()->noContent();
    }
}

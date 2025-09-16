<?php

namespace App\Services\Tag;

use App\Repositories\Contracts\TagRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class DeleteTagService
{
    public function __construct(private TagRepositoryInterface $repository)
    {
    }

    public function execute(int $id): void
    {
        $deletedCount = $this->repository->delete($id);

        if ($deletedCount === 0) {
            throw new NotFoundHttpException('Tag not found.');
        }
    }
}

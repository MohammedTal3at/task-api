<?php

namespace App\Services\Auth\Traits;

use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait AuthorizationTrait
{
    private function canUserManageTask(TaskRepositoryInterface $taskRepository, User $user, int $taskId): void
    {
        if ($user->isAdmin())
            return;

        if (!$taskRepository->canManageTask($taskId, $user->id)) {
            throw new AccessDeniedHttpException('Not allowed to perform this action');
        }
    }
}

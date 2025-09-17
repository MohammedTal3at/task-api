<?php

namespace Tests\Feature\Task;

use App\Events\TaskRestored;
use App\Models\Task;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RestoreTaskTest extends TestCase
{
    use RefreshDatabase;


    public function test_admin_can_restore_a_soft_deleted_task(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $task = Task::factory()->create();
        Event::fake();
        $task->delete();

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);

        // Act
        $response = $this->actingAs($admin)->patchJson('api/tasks/' . $task->id . '/restore');

        // Assert
        $response->assertOk();
        $this->assertNotSoftDeleted('tasks', ['id' => $task->id]);
        Event::assertDispatched(TaskRestored::class, function ($event) use ($task) {
            return $event->taskId === $task->id;
        });

    }


    public function test_non_admin_user_cannot_restore_a_task(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create();
        $task->delete();

        // Act
        $response = $this->actingAs($user)->patchJson('api/tasks/' . $task->id . '/restore');

        // Assert
        $response->assertForbidden();
    }


    public function test_restoring_a_non_existent_task_returns_not_found(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $nonExistentTaskId = 100;

        // Act
        $response = $this->actingAs($admin)->patchJson('api/tasks/' . $nonExistentTaskId . '/restore');

        // Assert
        $response->assertNotFound();
    }
}

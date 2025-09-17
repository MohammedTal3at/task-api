<?php

namespace Tests\Feature\Task;

use App\Events\TaskDeleted;
use App\Events\TaskStatusToggled;
use App\Models\Task;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;


    public function test_admin_can_soft_delete_a_task(): void
    {
        // Arrange: Create an admin user and a task
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $task = Task::factory()->create();
        Event::fake();

        // Act
        $response = $this->actingAs($admin)->deleteJson('api/tasks/' . $task->id);

        // Assert
        $response->assertNoContent();
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
        Event::assertDispatched(TaskDeleted::class, function ($event) use ($task) {
            return $event->taskId === $task->id;
        });
    }

    public function test_non_admin_user_cannot_delete_a_task(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create();

        // Act
        $response = $this->actingAs($user)->deleteJson('api/tasks/' . $task->id);

        // Assert
        $response->assertForbidden();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'deleted_at' => null]);
    }


    public function test_deleting_a_non_existent_task_returns_not_found(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $nonExistentTaskId = 100;

        // Act
        $response = $this->actingAs($admin)->deleteJson('api/tasks/' . $nonExistentTaskId);

        // Assert
        $response->assertNotFound();
    }
}

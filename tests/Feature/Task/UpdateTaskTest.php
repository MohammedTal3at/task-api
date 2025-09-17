<?php

namespace Tests\Feature\Task;

use App\Events\TaskUpdated;
use App\Models\Task;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;


    public function test_admin_can_update_a_task_successfully(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'version' => 1,
        ]);
        $updateData = [
            'title' => 'Updated Title by Admin',
            'status' => TaskStatus::COMPLETED->value,
        ];

        Event::fake();

        // Act
        $response = $this->actingAs($admin)->putJson('api/tasks/' . $task->id, $updateData);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Updated Title by Admin']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title by Admin',
            'status' => TaskStatus::COMPLETED->value,
            'version' => 2, // Version should be incremented
        ]);


        Event::assertDispatched(TaskUpdated::class, function ($event) use ($task) {
            return $event->taskId === $task->id;
        });

    }


    public function test_non_admin_user_cannot_update_a_task(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create();
        $updateData = ['title' => 'Attempted Update by Non-Admin'];

        // Act
        $response = $this->actingAs($user)->putJson('api/tasks/' . $task->id, $updateData);

        // Assert
        $response->assertForbidden();
    }


    public function test_update_fails_if_due_date_is_in_the_past_for_in_progress_task(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $task = Task::factory()->create(['status' => TaskStatus::IN_PROGRESS]);
        $updateData = [
            'due_date' => now()->subDay()->toDateString(),
        ];

        // Act
        $response = $this->actingAs($admin)->putJson('api/tasks/' . $task->id, $updateData);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('due_date');
    }


    public function test_update_succeeds_if_due_date_is_in_the_past_for_completed_task(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $task = Task::factory()->create();
        $updateData = [
            'due_date' => now()->subDay()->toDateString(),
            'status' => TaskStatus::COMPLETED->value,
        ];

        // Act
        $response = $this->actingAs($admin)->putJson('api/tasks/' . $task->id, $updateData);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => TaskStatus::COMPLETED->value,
        ]);
    }
}

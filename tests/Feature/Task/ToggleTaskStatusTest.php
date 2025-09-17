<?php

namespace Tests\Feature\Task;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Events\TaskStatusToggled;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ToggleTaskStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_his_task_status_successfully(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create(['status' => TaskStatus::PENDING, 'assigned_to' => $user->id]);
        $nextStatus = TaskStatus::IN_PROGRESS;
        Event::fake();

        // Act
        $response = $this->actingAs($user)->patch('api/tasks/' . $task->id . '/toggle-status');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => $nextStatus->value, 'id' => $task->id]);

        Event::assertDispatched(TaskStatusToggled::class, function ($event) use ($task) {
            return $event->taskId === $task->id;
        });
    }

    public function test_user_cant_toggle_another_users_task_status(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $anotherUser = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create(['status' => TaskStatus::PENDING, 'assigned_to' => $anotherUser->id]);

        // Act
        $response = $this->actingAs($user)->patch('api/tasks/' . $task->id . '/toggle-status');

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_toggle_any_task_status(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create(['status' => TaskStatus::PENDING, 'assigned_to' => $user->id]);
        $nextStatus = TaskStatus::IN_PROGRESS;
        Event::fake();

        // Act
        $response = $this->actingAs($admin)->patch('api/tasks/' . $task->id . '/toggle-status');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => $nextStatus->value, 'id' => $task->id]);

        Event::assertDispatched(TaskStatusToggled::class, function ($event) use ($task) {
            return $event->taskId === $task->id;
        });
    }

    public function test_task_status_cycle_works_correctly(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);

        $pendingTask = Task::factory()->create(['status' => TaskStatus::PENDING, 'assigned_to' => $user->id]);
        $inProgressTask = Task::factory()->create(['status' => TaskStatus::IN_PROGRESS, 'assigned_to' => $user->id]);
        $completedTask = Task::factory()->create(['status' => TaskStatus::COMPLETED, 'assigned_to' => $user->id]);;


        // Act
        $pendingTaskResponse = $this->actingAs($user)->patch('api/tasks/' . $pendingTask->id . '/toggle-status');
        $inProgressTaskResponse = $this->actingAs($user)->patch('api/tasks/' . $inProgressTask->id . '/toggle-status');
        $completedTaskResponse = $this->actingAs($user)->patch('api/tasks/' . $completedTask->id . '/toggle-status');

        // Assert
        $pendingTaskResponse->assertOk();
        $inProgressTaskResponse->assertOk();
        $completedTaskResponse->assertOk();


        $pendingTaskResponse->assertJsonFragment(['status' => TaskStatus::IN_PROGRESS->value, 'id' => $pendingTask->id]);
        $inProgressTaskResponse->assertJsonFragment(['status' => TaskStatus::COMPLETED->value, 'id' => $inProgressTask->id]);
        $completedTaskResponse->assertJsonFragment(['status' => TaskStatus::PENDING->value, 'id' => $completedTask->id]);
    }
}

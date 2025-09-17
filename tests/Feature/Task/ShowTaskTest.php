<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTaskTest extends TestCase
{
    use RefreshDatabase;


    public function test_admin_can_view_any_task(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => $otherUser->id]);

        // Act
        $response = $this->actingAs($admin)->getJson('api/tasks/' . $task->id);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $task->id]);
    }


    public function test_assigned_user_can_view_their_task(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create(['assigned_to' => $user->id]);

        // Act
        $response = $this->actingAs($user)->getJson('api/tasks/' . $task->id);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $task->id]);
    }


    public function test_unassigned_user_cannot_view_a_task(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $anotherUser = User::factory()->create(['role' => UserRole::USER]);
        $task = Task::factory()->create(['assigned_to' => $user->id]);

        // Act
        $response = $this->actingAs($anotherUser)->getJson('api/tasks/' . $task->id);

        // Assert
        $response->assertForbidden();
    }

    public function test_viewing_a_non_existent_task_returns_not_found(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $nonExistentTaskId = 100;

        // Act
        $response = $this->actingAs($admin)->getJson('api/tasks/' . $nonExistentTaskId);

        // Assert
        $response->assertNotFound();
    }
}

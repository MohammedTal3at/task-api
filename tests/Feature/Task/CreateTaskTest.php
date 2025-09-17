<?php

namespace Tests\Feature\Task;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Events\TaskCreated;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_admin_can_create_task_successfully(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $assignee = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        Event::fake();

        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => TaskStatus::PENDING->value,
            'assigned_to' => $assignee->id,
            'tags' => $tags->pluck('id')->toArray(),
            'due_date' => $this->faker->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
        ];

        // Act
        $response = $this->actingAs($admin)->postJson('/api/tasks', $taskData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'description', 'status', 'priority', 'due_date', 'assignee', 'tags', 'metadata'
                ]
            ])
            ->assertJson(['data' => ['title' => $taskData['title']]]);

        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'assigned_to' => $assignee->id,
        ]);

        $this->assertDatabaseCount('tag_task', 2);

        $responseData = $response->json('data');
        $createdTaskId = $responseData['id'];
        Event::assertDispatched(TaskCreated::class, function ($event) use ($createdTaskId) {
            return $event->taskId === $createdTaskId;
        });
    }

    public function test_non_admin_user_cannot_create_task(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $taskData = ['title' => 'This should not be created'];

        // Act
        $response = $this->actingAs($user)->postJson('/api/tasks', $taskData);

        // Assert
        $response->assertStatus(403); // Forbidden
    }

    public function test_create_task_fails_with_short_title(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $taskData = ['title' => 'abc']; // Title is too short

        // Act
        $response = $this->actingAs($admin)->postJson('/api/tasks', $taskData);

        // Assert
        $response->assertStatus(422) // Unprocessable Entity
        ->assertJsonValidationErrors('title');
    }

    public function test_create_task_fails_with_past_due_date_for_pending_status(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $taskData = [
            'title' => $this->faker->sentence,
            'status' => TaskStatus::PENDING->value,
            'due_date' => now()->subDay()->format('Y-m-d'),
        ];

        // Act
        $response = $this->actingAs($admin)->postJson('/api/tasks', $taskData);

        // Assert
        $response->assertStatus(422)
        ->assertJsonValidationErrors('due_date');
    }
}

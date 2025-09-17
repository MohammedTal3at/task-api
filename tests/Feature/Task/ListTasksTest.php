<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use App\Models\Tag;
use App\Enums\UserRole;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ListTasksTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_list_all_tasks_from_all_users(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Task::factory()->create(['assigned_to' => $user1->id]);
        Task::factory()->create(['assigned_to' => $user2->id]);

        // Act
        $response = $this->actingAs($admin)->getJson('api/tasks');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }


    public function test_regular_user_can_only_list_their_own_assigned_tasks(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::USER]);
        $otherUser = User::factory()->create();
        $userTask = Task::factory()->create(['assigned_to' => $user->id]);
        Task::factory()->create(['assigned_to' => $otherUser->id]);

        // Act
        $response = $this->actingAs($user)->getJson('api/tasks');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $userTask->id]);
    }

    public function test_filtering_by_multiple_statuses_works_correctly(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Task::factory()->create(['status' => TaskStatus::PENDING]);
        Task::factory()->create(['status' => TaskStatus::IN_PROGRESS]);
        Task::factory()->create(['status' => TaskStatus::COMPLETED]);

        // Act
        $response = $this->actingAs($admin)->getJson('api/tasks?status=pending,in_progress');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonMissing(['status' => TaskStatus::COMPLETED->value]);
    }


    public function test_filtering_by_tags_works_correctly(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $task1 = Task::factory()->hasAttached($tag1)->create();
        Task::factory()->hasAttached($tag2)->create();

        // Act
        $response = $this->actingAs($admin)->getJson('api/tasks?tags=' . $tag1->id);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $task1->id]);
    }

    public function test_sorting_by_due_date_asc_works_correctly(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $taskSooner = Task::factory()->create(['due_date' => now()->addDays(2)]);
        $taskLater = Task::factory()->create(['due_date' => now()->addDays(5)]);

        // Act
        $response = $this->actingAs($admin)->getJson('api/tasks?' . http_build_query(['sort_by' => 'due_date', 'sort_order' => 'asc']));

        // Assert
        $response->assertOk();
        $this->assertEquals($taskSooner->id, $response->json('data.0.id'));
        $this->assertEquals($taskLater->id, $response->json('data.1.id'));
    }

    public function test_cursor_pagination_is_activated_when_cursor_is_provided(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Task::factory(5)->create();

        // Act
        $initialResponse = $this->actingAs($admin)->getJson('api/tasks?' . http_build_query(['per_page' => 2, 'cursor' => true]));
        $cursor = $initialResponse->json('meta.next_cursor');

        // Act again
        $response = $this->actingAs($admin)->getJson('api/tasks?' . http_build_query(['per_page' => 2, 'cursor' => $cursor]));

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta' => [
                'path',
                'per_page',
                'next_cursor',
                'prev_cursor',
            ],
        ]);
        $response->assertJsonMissingPath('meta.total');
    }

    public function test_offset_pagination_works_by_default(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Task::factory(5)->create();

        // Act
        $response = $this->actingAs($admin)->getJson('api/tasks?' . http_build_query(['per_page' => 2]));

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta' => [
                'path',
                'per_page',
                'to',
                'total',
                'last_page',
            ],
        ]);
        $response->assertJsonMissingPath('meta.next_cursor');
    }

    public function test_filtering_by_keyword_works_correctly(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $matchingTask1 = Task::factory()->create([
            'title' => 'Fix database indexing issue',
            'description' => 'This involves fulltext search optimization',
        ]);
        $matchingTask2 = Task::factory()->create([
            'title' => 'Migration plan',
            'description' => 'Optimize database queries for search performance',
        ]);
        $nonMatchingTask = Task::factory()->create([
            'title' => 'Frontend UI update',
            'description' => 'Improve button styling',
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->getJson('api/tasks?keyword=database');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');

        $returnedTaskIds = collect($response->json('data'))->pluck('id');

        $this->assertTrue($returnedTaskIds->contains($matchingTask1->id));
        $this->assertTrue($returnedTaskIds->contains($matchingTask2->id));
        $this->assertFalse($returnedTaskIds->contains($nonMatchingTask->id));

    }

}

<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_tasks_for_their_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $otherProject = Project::factory()->create();

        Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Visible task',
        ]);
        Task::factory()->create([
            'project_id' => $otherProject->id,
            'title' => 'Hidden task',
        ]);

        Sanctum::actingAs($user);

        $this->getJson("/api/projects/{$project->id}/tasks")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Visible task')
            ->assertJsonMissing(['title' => 'Hidden task']);
    }

    public function test_user_can_create_task_for_their_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'New task',
            'description' => 'Task description',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => '2026-06-15',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'New task')
            ->assertJsonPath('data.project_id', $project->id);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New task',
            'project_id' => $project->id,
        ]);
    }

    public function test_user_can_show_their_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $task->id);
    }

    public function test_user_can_update_their_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        Sanctum::actingAs($user);

        $this->patchJson("/api/tasks/{$task->id}", [
            'title' => 'Updated task',
            'description' => 'Updated description',
            'status' => 'done',
            'priority' => 'low',
            'due_date' => null,
        ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated task')
            ->assertJsonPath('data.status', 'done');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated task',
            'status' => 'done',
        ]);
    }

    public function test_user_can_delete_their_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        Sanctum::actingAs($user);

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Task deleted successfully');

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_access_tasks_from_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['owner_id' => $otherUser->id]);
        $task = Task::factory()->create(['project_id' => $otherProject->id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/projects/{$otherProject->id}/tasks")->assertNotFound();
        $this->postJson("/api/projects/{$otherProject->id}/tasks", [
            'title' => 'No access',
            'description' => null,
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => null,
        ])->assertNotFound();
        $this->getJson("/api/tasks/{$task->id}")->assertNotFound();
        $this->deleteJson("/api/tasks/{$task->id}")->assertNotFound();
    }

    public function test_task_payload_is_validated(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->postJson("/api/projects/{$project->id}/tasks", [
            'title' => '',
            'status' => 'blocked',
            'priority' => 'urgent',
            'due_date' => 'not-a-date',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'status', 'priority', 'due_date']);
    }

    public function test_tasks_require_authentication(): void
    {
        $project = Project::factory()->create();

        $this->getJson("/api/projects/{$project->id}/tasks")->assertUnauthorized();
    }
}

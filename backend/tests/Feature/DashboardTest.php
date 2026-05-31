<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_statistics_for_current_user(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $otherProject = Project::factory()->create();

        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'todo',
        ]);
        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'in_progress',
        ]);
        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'done',
        ]);
        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'done',
        ]);
        Task::factory()->create([
            'project_id' => $otherProject->id,
            'status' => 'done',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('projects_count', 1)
            ->assertJsonPath('tasks_count', 4)
            ->assertJsonPath('todo_count', 1)
            ->assertJsonPath('in_progress_count', 1)
            ->assertJsonPath('done_count', 2)
            ->assertJsonPath('completion_rate', 50)
            ->assertJsonCount(1, 'recent_projects')
            ->assertJsonCount(4, 'recent_tasks');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }
}

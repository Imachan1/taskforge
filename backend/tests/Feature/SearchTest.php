<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_current_users_matching_projects_and_tasks(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Test Launch',
            'description' => 'Visible test project',
        ]);
        $otherProject = Project::factory()->create([
            'name' => 'Test Hidden',
            'description' => 'Should not be visible',
        ]);

        Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Test checklist',
            'description' => 'Visible task',
        ]);
        Task::factory()->create([
            'project_id' => $otherProject->id,
            'title' => 'Test private task',
            'description' => 'Should not be visible',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/search?q=test')
            ->assertOk()
            ->assertJsonCount(1, 'projects')
            ->assertJsonCount(1, 'tasks')
            ->assertJsonPath('projects.0.name', 'Test Launch')
            ->assertJsonPath('tasks.0.title', 'Test checklist')
            ->assertJsonPath('tasks.0.project_id', $project->id)
            ->assertJsonPath('tasks.0.project_name', 'Test Launch')
            ->assertJsonMissing(['name' => 'Test Hidden'])
            ->assertJsonMissing(['title' => 'Test private task']);
    }

    public function test_empty_search_returns_empty_results(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/search?q=')
            ->assertOk()
            ->assertExactJson([
                'projects' => [],
                'tasks' => [],
            ]);
    }

    public function test_search_requires_authentication(): void
    {
        $this->getJson('/api/search?q=test')->assertUnauthorized();
    }
}

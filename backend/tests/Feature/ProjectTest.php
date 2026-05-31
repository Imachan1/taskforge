<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_only_their_projects(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Project::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Visible project',
        ]);

        Project::factory()->create([
            'owner_id' => $otherUser->id,
            'name' => 'Hidden project',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/projects');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Visible project')
            ->assertJsonMissing(['name' => 'Hidden project']);
    }

    public function test_user_can_create_project(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/projects', [
            'name' => 'New project',
            'description' => 'Project description',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'New project')
            ->assertJsonPath('data.owner_id', $user->id);

        $this->assertDatabaseHas('projects', [
            'name' => 'New project',
            'owner_id' => $user->id,
        ]);
    }

    public function test_user_can_show_their_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $project->id);
    }

    public function test_user_can_update_their_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->putJson("/api/projects/{$project->id}", [
            'name' => 'Updated project',
            'description' => 'Updated description',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated project');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated project',
        ]);
    }

    public function test_user_can_delete_their_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->deleteJson("/api/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Project deleted successfully');

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_user_cannot_access_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/projects/{$project->id}")->assertNotFound();
        $this->putJson("/api/projects/{$project->id}", [
            'name' => 'Stolen project',
            'description' => 'Nope',
        ])->assertNotFound();
        $this->deleteJson("/api/projects/{$project->id}")->assertNotFound();
    }

    public function test_project_name_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/projects', [
            'name' => '',
            'description' => 'Missing name',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_projects_require_authentication(): void
    {
        $this->getJson('/api/projects')->assertUnauthorized();
    }
}

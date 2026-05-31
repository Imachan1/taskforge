<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'valeriia@test.com'],
            [
                'name' => 'Valeriia',
                'password' => 'password123',
            ],
        );

        $projects = [
            [
                'name' => 'TaskForge Launch',
                'description' => 'Prepare the initial workspace, authentication, and project planning flow.',
            ],
            [
                'name' => 'Client Portal',
                'description' => 'Collect client requirements and track delivery milestones.',
            ],
            [
                'name' => 'Internal Automation',
                'description' => 'Automate recurring reporting and team status workflows.',
            ],
        ];

        foreach ($projects as $project) {
            $createdProject = Project::firstOrCreate(
                [
                    'owner_id' => $user->id,
                    'name' => $project['name'],
                ],
                ['description' => $project['description']],
            );

            $tasks = [
                [
                    'title' => 'Define scope',
                    'description' => 'Write the first version of the project scope and constraints.',
                    'status' => 'todo',
                    'priority' => 'high',
                    'due_date' => now()->addWeek()->toDateString(),
                ],
                [
                    'title' => 'Review milestones',
                    'description' => 'Check project milestones and align next delivery steps.',
                    'status' => 'in_progress',
                    'priority' => 'medium',
                    'due_date' => now()->addWeeks(2)->toDateString(),
                ],
                [
                    'title' => 'Archive notes',
                    'description' => 'Collect completed notes and store them with the project.',
                    'status' => 'done',
                    'priority' => 'low',
                    'due_date' => null,
                ],
            ];

            foreach ($tasks as $task) {
                Task::firstOrCreate(
                    [
                        'project_id' => $createdProject->id,
                        'title' => $task['title'],
                    ],
                    $task,
                );
            }
        }
    }
}

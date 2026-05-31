<?php

namespace Database\Seeders;

use App\Models\Project;
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
            Project::firstOrCreate(
                [
                    'owner_id' => $user->id,
                    'name' => $project['name'],
                ],
                ['description' => $project['description']],
            );
        }
    }
}

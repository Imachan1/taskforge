<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $projectsCount = Project::where('owner_id', $userId)->count();

        $tasksQuery = Task::whereHas('project', function ($query) use ($userId) {
            $query->where('owner_id', $userId);
        });

        $tasksCount = (clone $tasksQuery)->count();
        $todoCount = (clone $tasksQuery)->where('status', 'todo')->count();
        $inProgressCount = (clone $tasksQuery)->where('status', 'in_progress')->count();
        $doneCount = (clone $tasksQuery)->where('status', 'done')->count();
        $completionRate = $tasksCount > 0
            ? (int) round(($doneCount / $tasksCount) * 100)
            : 0;

        $recentProjects = Project::where('owner_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        $recentTasks = Task::with('project:id,name,owner_id')
            ->whereHas('project', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'project_id' => $task->project_id,
                'project_name' => $task->project->name,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ]);

        return response()->json([
            'projects_count' => $projectsCount,
            'tasks_count' => $tasksCount,
            'todo_count' => $todoCount,
            'in_progress_count' => $inProgressCount,
            'done_count' => $doneCount,
            'completion_rate' => $completionRate,
            'recent_projects' => $recentProjects,
            'recent_tasks' => $recentTasks,
        ]);
    }
}

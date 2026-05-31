<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $query = trim($validated['q'] ?? '');

        if ($query === '') {
            return response()->json([
                'projects' => [],
                'tasks' => [],
            ]);
        }

        $userId = $request->user()->id;
        $like = "%{$query}%";

        $projects = Project::where('owner_id', $userId)
            ->where(function ($builder) use ($like) {
                $builder
                    ->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            })
            ->latest()
            ->limit(10)
            ->get();

        $tasks = Task::with('project:id,name,owner_id')
            ->whereHas('project', function ($builder) use ($userId) {
                $builder->where('owner_id', $userId);
            })
            ->where(function ($builder) use ($like) {
                $builder
                    ->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like);
            })
            ->latest()
            ->limit(10)
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
            'projects' => $projects,
            'tasks' => $tasks,
        ]);
    }
}

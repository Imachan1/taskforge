<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProjectOwner($request, $project);

        $tasks = $project->tasks()
            ->latest()
            ->get();

        return response()->json([
            'data' => $tasks,
        ]);
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $this->authorizeProjectOwner($request, $project);

        $task = $project->tasks()->create($request->validated());

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        $this->authorizeTaskOwner($request, $task);

        return response()->json([
            'data' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorizeTaskOwner($request, $task);

        $task->update($request->validated());

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task->fresh(),
        ]);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->authorizeTaskOwner($request, $task);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    private function authorizeProjectOwner(Request $request, Project $project): void
    {
        abort_if($project->owner_id !== $request->user()->id, 404);
    }

    private function authorizeTaskOwner(Request $request, Task $task): void
    {
        $task->loadMissing('project');

        abort_if($task->project->owner_id !== $request->user()->id, 404);
    }
}

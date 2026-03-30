<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // GET /api/tasks
    public function index()
    {
        return response()->json(Task::all());
    }

    // POST /api/tasks
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);

        $exists = Task::where('title', $validated['title'])
            ->where('due_date', $validated['due_date'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Task with same title and due date already exists'
            ], 400);
        }

        $task = Task::create([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

        return response()->json($task, 201);
    }

    // PATCH /api/tasks/{id}/status
    public function updateStatus($id, Request $request)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $task->status = $request->status;
        $task->save();

        return response()->json($task);
    }

    // DELETE /api/tasks/{id}
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    // GET /api/tasks/report
    public function report()
    {
        return response()->json([
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'done' => Task::where('status', 'done')->count(),
        ]);
    }
}
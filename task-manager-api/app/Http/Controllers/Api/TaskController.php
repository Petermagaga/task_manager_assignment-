<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{

    public function index()
    {
        return response()->json(Task::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                // Laravel PRO validation (better than manual check)
                \Illuminate\Validation\Rule::unique('tasks')->where(function ($query) use ($request) {
                    return $query->where('due_date', $request->due_date);
                }),
            ],
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => ['required', \Illuminate\Validation\Rule::in(['low', 'medium', 'high'])],
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }


    
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

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

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
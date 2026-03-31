<?php  

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{


    public function index(Request $request)
    {
        $query = Task::query();

       
        if ($request->has('status')) {

            $allowedStatus = ['pending', 'in_progress', 'done'];

            if (!in_array($request->status, $allowedStatus)) {
                return response()->json([
                    'message' => 'Invalid status value'
                ], 400);
            }

            $query->where('status', $request->status);
        }

        
        $tasks = $query
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        
        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found',
                'data' => []
            ], 200);
        }

        
        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'data' => $tasks
        ], 200);
    }    


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                
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



    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,done'
        ]);

        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        $currentStatus = $task->status;
        $newStatus = $validated['status'];

        $allowedTransitions = [
            'pending' => ['in_progress'],
            'in_progress' => ['done'],
            'done' => []
        ];

        
        if (!isset($allowedTransitions[$currentStatus])) {
            return response()->json([
                'message' => 'Invalid current status in database'
            ], 500);
        }

        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            return response()->json([
                'message' => "Invalid status transition from $currentStatus to $newStatus"
            ], 400);
        }

        $task->status = $newStatus;
        $task->save();

        return response()->json($task, 200);
    }


    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        
        if ($task->status !== 'done') {
            return response()->json([
                'message' => 'Only completed tasks can be deleted'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ], 200); 
    }


    public function report(Request $request)
    {
        
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = $request->date;

        
        $tasks = Task::whereDate('due_date', $date)->get();

        
        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending', 'in_progress', 'done'];

        $summary = [];

        foreach ($priorities as $priority) {
            $summary[$priority] = [];

            foreach ($statuses as $status) {
                $summary[$priority][$status] = 0;
            }
        }

        foreach ($tasks as $task) {
            $summary[$task->priority][$task->status]++;
        }


        return response()->json([
            'date' => $date,
            'summary' => $summary
        ], 200);
    }
    }
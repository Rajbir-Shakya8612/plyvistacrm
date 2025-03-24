<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        $tasks = Task::with('assignee')
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->assignee_id, function($query, $assigneeId) {
                $query->where('assignee_id', $assigneeId);
            })
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($tasks);
        }

        $salespersons = User::where('role', 'salesperson')->get(['id', 'name']);
        return view('admin.tasks.index', compact('tasks', 'salespersons'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'in:lead,sale,meeting'],
            'assignee_id' => ['required', 'exists:users,id'],
            'due_date' => ['required', 'date'],
        ]);

        $validated['status'] = 'todo';
        $task = Task::create($validated);
        $task->load('assignee');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task
            ], 201);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully');
    }

    /**
     * Display the specified task.
     */
    public function show(Request $request, Task $task)
    {
        $task->load('assignee');

        if ($request->wantsJson()) {
            return response()->json($task);
        }

        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'type' => ['sometimes', 'required', 'string', 'in:lead,sale,meeting'],
            'status' => ['sometimes', 'required', 'string', 'in:todo,in_progress,done'],
            'assignee_id' => ['sometimes', 'required', 'exists:users,id'],
            'due_date' => ['sometimes', 'required', 'date'],
        ]);

        if (isset($validated['status']) && $validated['status'] === 'done') {
            $validated['completed_at'] = now();
        }

        $task->update($validated);
        $task->load('assignee');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Task updated successfully',
                'task' => $task
            ]);
        }

        return back()->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Task deleted successfully'
            ]);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully');
    }
} 
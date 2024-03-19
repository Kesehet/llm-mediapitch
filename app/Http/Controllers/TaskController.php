<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function getPendingTasks()
    {
        $tasks = Task::where('status', 'pending')->get();
        return response()->json($tasks);
    }

    public function updateTask(Request $request, $uuid)
    {
        $task = Task::where('uuid', $uuid)->firstOrFail();
    
        $validated = $request->validate([
            'status' => 'in:pending,processing,completed',
            'result' => 'nullable',
        ]);
        $task->update($validated);
        return response()->json($task);
    }


    public function handleStuckTasks(){
        Task::where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes(20)) // Adjust time as needed
            ->update(['status' => 'pending']);
            return response()->json(['message' => 'Stuck tasks handled successfully.']);
    }
    
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function getPendingTasks($type)
    {
        $tasks = Task::where('status', 'pending')->where('task_type', $type)->get();
        // get first task 
        $task = $tasks->first();
        // update the first task and set status to processing
        $task->update(['status' => 'processing']);
        return response()->json($task);
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

    public function getResponse($uuid){
        $task = Task::where('uuid', $uuid)->where('status', 'completed')->firstOrFail();
        return response()->json($task->makeHidden(['id', 'payload']));
    }
    

    public function handleStuckTasks(){
        Task::where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes(20)) // Adjust time as needed
            ->update(['status' => 'pending']);
            return response()->json(['message' => 'Stuck tasks handled successfully.']);
    }
}


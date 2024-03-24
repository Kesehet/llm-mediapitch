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
        if($task){
            // update the first task and set status to processing
            $task->update(['status' => 'processing']);
        }
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
        return response()->json([
            "result"=>$task->result,
            "status"=>$task->status,
            "uuid"=>$task->uuid,
            "hash"=>$task->description,
            "created_at"=>$task->created_at,
            "updated_at"=>$task->updated_at
        ]);
    }
    

    public function handleStuckTasks(){
        // Handle stuck tasks
        Task::where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes(5)) // Adjust time as needed
            ->update(['status' => 'pending']);
    
        // Delete completed tasks that are 1 day old
        Task::where('status', 'completed')
            ->where('updated_at', '<', now()->subDay(7))
            ->delete();
    
        return response()->json(['message' => 'Stuck and old completed tasks handled successfully.']);
    }
    
}


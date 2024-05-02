<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Services\InstanceService;
use App\Services\NotificationService;

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
            ->where('updated_at', '<', now()->subMinutes(1)) // Adjust time as needed
            ->update(['status' => 'pending']);
    
        // Delete completed tasks that are 1 day old
        Task::where('created_at', '<', now()->subMinutes(60*24))
            ->delete();

        // If there are no pending tasks
        if(!Task::where('status', 'pending')->exists()){
            $instanceService = new InstanceService();
            // get all machines with status true
            $activeMachines = Machine::where('status', true)->get();
            $count = count($activeMachines);
            foreach ($activeMachines as $machine) {
                $instanceService->destroyInstance($machine->machine_id);
            }
            if ($count > 0) {
                NotificationService::send('I have destroyed all my instances because they were sitting idle. There was no task to perform. Please find the instance status here https://cloud.vast.ai/instances/. Thank you.');
            }
            
            return response()->json(['message' => 'No pending tasks found.']);
        }
    
        return response()->json(['message' => 'Stuck and old completed tasks handled successfully.']);
    }
    
}


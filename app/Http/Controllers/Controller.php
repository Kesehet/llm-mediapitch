<?php

namespace App\Http\Controllers;

use App\Services\NgrokService;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedForCredentialsHttpException;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


use App\Models\Machine;
use App\Services\InstanceService;
use App\Services\NotificationService;

use App\Models\Task;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        
    }

    public function index()
    {
        return view('welcome', ['tasks' => Task::all(), 'machines' => Machine::all()]);
    }


    
    public function llm(Request $request)
    {
        $this->checkMachine();
        // Prepare your payload and task type
        $payload = ["query" => $request->input('query')];
        $taskType = 'llm';
    
        // Generate a consistent hash of the task_type and serialized payload
        $hashInput = $taskType . serialize($payload);
        $descriptionHash = hash('sha256', $hashInput); // Using SHA-256 for hashing
    
        // Search for an existing task with the same hash
        $existingTask = Task::where('description', $descriptionHash)->first();
    
        if ($existingTask) {
            // Task already exists, return its UUID
            return response()->json(["id" => $existingTask->uuid, "message" => "Existing task found, using cached result."]);
        } else {
            // No existing task found, create a new one
            $task = Task::create([
                'uuid' => Str::uuid(), // Ensure UUID is generated
                'payload' => $payload,
                'task_type' => $taskType,
                'description' => $descriptionHash, // Store the consistent hash
                'status' => 'pending',
                'result' => null,
                'pingback_url' => $request->input('pingback_url') ?? null,
            ]);
            
            return response()->json(["id" => $task->uuid, "message" => "New task created."]);
        }
    }
    
    

    public function whisper(Request $request)
    {
        $this->checkMachine();
        // Prepare your payload and task type
        $payload = [
            "audio_url" => $request->input('audio_url'),
            "task" => $request->input('task'),
            "params" => $request->input('params'),
            "context" => $request->input('context'),
        ];
        
        $taskType = 'whisper';
    
        // Generate a consistent hash of the task_type and serialized payload
        $hashInput = $taskType . serialize($payload);
        $descriptionHash = hash('sha256', $hashInput); // Using SHA-256 for hashing
    
        // Search for an existing task with the same hash
        $existingTask = Task::where('description', $descriptionHash)->first();
    
        if ($existingTask) {
            // Task already exists, return its UUID
            return response()->json(["id" => $existingTask->uuid, "message" => "Existing task found, using cached result."]);
        } else {
            // No existing task found, create a new one
            $task = Task::create([
                'uuid' => Str::uuid(), // Ensure UUID is generated
                'payload' => $payload,
                'task_type' => $taskType,
                'description' => $descriptionHash, // Store the consistent hash
                'status' => 'pending',
                'result' => null,
                'pingback_url' => $request->input('pingback_url') ?? null,
            ]);
            
            return response()->json(["id" => $task->uuid, "message" => "New task created."]);
        }
    }

    function getTasks($task_id)
    {
        $urls = $this->index();
        $firstUrl = $urls->first();
        if (!$firstUrl) {
            return ['error' => 'No tunnels found or error in fetching tunnels.'];
        }

        $client = new Client();

        $response = $client->post($firstUrl . '/tasks/' . $task_id,[]);
        if ($response->getStatusCode() == 200) {
            return $response->getBody()->getContents();
        }

        return json_decode($response->getBody()->getContents());


    }

    function checkMachine(){
        $machines = Machine::where('status', true)->get();
        if(count($machines) == 0){
            $instanceService = new InstanceService();
            $machine = $instanceService->createInstance();
        }
    }
}
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

use App\Models\Task;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        
    }

    public function index()
    {
        return (new NgrokService())->getTunnels();
    }


    
    public function llm(Request $request)
    {
        // Pre-calculate the hash
        $payload = ["query" => $request->input('query')];
        $taskType = 'llm';
        $hashInput = $taskType . serialize($payload);
        $descriptionHash = Hash::make($hashInput);
    
        // Check for an existing task with the same hash
        $existingTask = Task::where('description', $descriptionHash)->first();
    
        if ($existingTask) {
            // Return the existing task if found
            return response()->json(["id" => $existingTask->uuid, "message" => "Existing task found."]);
        } else {
            // Create a new task if not found
            $task = Task::create([
                'uuid' => Str::uuid(), // Ensure UUID is generated
                'payload' => $payload,
                'task_type' => $taskType,
                'description' => $descriptionHash, // Store the pre-calculated hash
                'status' => 'pending',
                'result' => null
            ]);
            
            return response()->json(["id" => $task->uuid, "message" => "New task created."]);
        }
    }
    

    public function whisper(Request $request)
    {
        try {
            $urls = $this->index();
            $firstUrl = $urls->first();
            if (!$firstUrl) {
                return response()->json(['error' => 'No tunnels found or error in fetching tunnels.'], 400);
            }

            $client = new Client(); // Create a Guzzle HTTP client instance
            $response = $client->post($firstUrl . "/whisper", [
                'form_params' => [
                    'audio_url' => $request->input('audio_url'),
                ],
            ]);
            
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), '$response' => $response->getBody()->getContents() ], 400);
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
}
<?php
use Illuminate\Http\Request;
use App\Services\NgrokService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Carbon\Carbon;



//Route::middleware('auth:sanctum')->group(function () {
    Route::post('llm', [Controller::class, 'llm'] )->name('llm');
    Route::post('whisper', [Controller::class, 'whisper'])->name('whisper');
//});





Route::get('/clean', [TaskController::class, 'handleStuckTasks']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks/pending/{type}', [TaskController::class, 'getPendingTasks']);

Route::get('/tasks/response/{uuid}', [TaskController::class, 'getResponse']);

Route::patch('/tasks/{task}', [TaskController::class, 'updateTask']);


Route::get("/all-tasks", function () {
    $tasks = Task::where('status', 'pending')->orWhere('status', 'processing')->get();

    // Add Unix timestamp to each task object
    foreach ($tasks as $task) {
        $task->unix_timestamp = Carbon::parse($task->created_at)->timestamp;
    }

    return $tasks;
});






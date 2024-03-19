<?php
use Illuminate\Http\Request;
use App\Services\NgrokService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


Route::post('llm', [Controller::class, 'llm'] )->name('llm');
Route::post('whisper', [Controller::class, 'whisper'])->name('whisper');




Route::get('/clean', [TaskController::class, 'handleStuckTasks']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks/pending', [TaskController::class, 'getPendingTasks']);
Route::patch('/tasks/{task}', [TaskController::class, 'updateTask']);

Route::get('/tasks/response/{uuid}', [TaskController::class, 'getResponse']);



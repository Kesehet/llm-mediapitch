<?php
use Illuminate\Http\Request;
use App\Services\NgrokService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::get('tunnels', [Controller::class, 'index'])->name('tunnels');

Route::post('llm', [Controller::class, 'llm'] )->name('llm');

Route::post('whisper', [Controller::class, 'whisper'])->name('whisper');




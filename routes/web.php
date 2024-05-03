<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\LoginController;
use App\Services\InstanceService;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controller::class, 'index'])->name('home');




// Google OAuth Routes
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get("test", function () {
    $instanceService = new InstanceService();
    $instanceNow = $instanceService->createInstance();
    return response()->json($instanceNow);
});
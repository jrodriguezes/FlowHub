<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\AutomationExecutionController;
use App\Http\Controllers\ServiceConnectionController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    // Home Page
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Automations pages & crud
    Route::get('/automations', [AutomationController::class, 'index'])->name('automations.index');
    Route::post('/automations', [AutomationController::class, 'store'])->name('automations.store');
    Route::get('/automations/{automation}', [AutomationController::class, 'show'])->name('automations.show');
    Route::patch('/automations/{automation}/toggle', [AutomationController::class, 'toggle'])->name('automations.toggle');
    Route::put('/automations/{automation}', [AutomationController::class, 'update'])->name('automations.update');
    Route::delete('/automations/{automation}', [AutomationController::class, 'destroy'])->name('automations.destroy');

    // Connections pages & crud
    Route::get('/connections', [ServiceConnectionController::class, 'index'])->name('connections.index');
    Route::get('/connections/{serviceConnection}', [ServiceConnectionController::class, 'show'])->name('connections.show');
    Route::put('/connections/{serviceConnection}', [ServiceConnectionController::class, 'update'])->name('connections.update');
    Route::patch('/connections/{serviceConnection}', [ServiceConnectionController::class, 'destroy'])->name('connections.destroy');

    // Executions pages & crud
    Route::get('/executions', [AutomationExecutionController::class, 'index'])->name('executions.index');
    Route::get('/executions/{automationExecution}', [AutomationExecutionController::class, 'show'])->name('executions.show');
    Route::put('/executions/{automationExecution}', [AutomationExecutionController::class, 'update'])->name('executions.update');
    Route::delete('/executions/{automationExecution}', [AutomationExecutionController::class, 'destroy'])->name('executions.destroy');

    // Google services
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect'); // user button to redirect to google servers(connect with google)
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'googleCallback'])->name('google.callback'); // the url where the user is redirected after the authentication and give permissions 
});


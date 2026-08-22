<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\AutomationExecutionController;
use App\Http\Controllers\ServiceConnectionController;
use App\Http\Controllers\GitHubAuthController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GitHubWebhookController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // 2FA Challenge Frontend Route
    Route::get('/2fa-challenge', [TwoFactorChallengeController::class, 'show'])->name('2fa.challenge');

    Route::post('/2fa-challenge', [TwoFactorChallengeController::class, 'verify'])->name('2fa.challenge.verify');
});

Route::middleware('auth')->group(function () {
    // Home Page
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // profile page
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/2fa/enable', [ProfileController::class, 'enable'])->name('2fa.enable');
    Route::post('/profile/2fa/confirm', [ProfileController::class, 'confirm'])->name('2fa.confirm');
    Route::post('/profile/2fa/disable', [ProfileController::class, 'disable'])->name('2fa.disable');

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
    Route::delete('/connections/{serviceConnection}', [ServiceConnectionController::class, 'destroy'])->name('connections.destroy');

    // Executions pages & crud
    Route::get('/executions', [AutomationExecutionController::class, 'index'])->name('executions.index');
    Route::get('/executions/{automationExecution}', [AutomationExecutionController::class, 'show'])->name('executions.show');
    Route::put('/executions/{automationExecution}', [AutomationExecutionController::class, 'update'])->name('executions.update');
    Route::delete('/executions/{automationExecution}', [AutomationExecutionController::class, 'destroy'])->name('executions.destroy');

    // Google services
    // user button to redirect to google servers(connect with google)
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
    // google server redirect to this url after user authenticates and authorize the app
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'googleCallback'])->name('google.callback');

    // Github services
    // user button to redirect to github servers(connect with github)
    Route::get('/auth/github', [GitHubAuthController::class, 'redirect'])->name('github.redirect');
    // github server redirect to this url after user authenticates and authorize the app
    Route::get('/auth/github/callback', [GitHubAuthController::class, 'callback'])->name('github.callback');
});

Route::post('/webhooks/github', [GitHubWebhookController::class, 'handle'])->name('webhooks.github');


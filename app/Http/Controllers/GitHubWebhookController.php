<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Services\ExecutionEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubWebhookController extends Controller
{
    public function handle(Request $request, ExecutionEngine $engine): JsonResponse
    {
        // cryptographic verification (the padlock)
        $secret = config('services.github.webhook_secret');

        if (!$secret) {
            Log::error('GitHub Webhook: No se ha configurado el secreto en el .env');
            return response()->json(['error' => 'Server misconfigured'], 500);
        }

        // github sends his signature in this header
        $signature = $request->header('x-hub-signature-256');

        // we verify if the signature is valid
        if (!$signature) {
            return response()->json(['error' => 'Missing signature'], 401);
        }
        // we calculate the signature ourselves using the secret password and the raw content
        $payloadBody = $request->getContent();
        $hash = 'sha256=' . hash_hmac('sha256', $payloadBody, $secret);

        // if our calculated signature does not match the one github sent, it's a hacker
        if (!hash_equals($hash, $signature)) {
            Log::warning('GitHub Webhook: Invalid signature detected');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // data extraction
        $event = $request->header('x-github-event'); // Ej: "push" o "issues"
        $payload = $request->all();

        // we inject the event name into the payload so the Condition Evaluator can use it
        $payload['github_event'] = $event;

        // start the Engine
        // we search for all the automations the user has turned on
        $activeAutomations = Automation::where('is_active', true)->get();
        foreach ($activeAutomations as $automation) {
            // the engine will be in charge of passing the payload to the condition evaluator.
            // if the evaluator says "yes, it is met", the engine will automatically dispatch the jobs to redis.
            $engine->process($automation, $payload);
        }
        // tell github "received, thank you."
        return response()->json(['message' => 'Webhook procesado con exito']);

    }
}

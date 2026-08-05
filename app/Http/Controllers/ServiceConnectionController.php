<?php

namespace App\Http\Controllers;

use App\Models\ServiceConnection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceConnectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceConnection::class);

        $connections = $request->user()
            ->serviceConnections()
            ->latest()
            ->get();

        return response()->json(['data' => $connections]);
    }

    public function show(ServiceConnection $serviceConnection): JsonResponse
    {
        $this->authorize('view', $serviceConnection);

        return response()->json(['data' => $serviceConnection]);
    }

    public function update(Request $request, ServiceConnection $serviceConnection): JsonResponse
    {
        $this->authorize('update', $serviceConnection);

        $validated = $request->validate([
            'status' => ['sometimes', 'string'],
        ]);

        $serviceConnection->update($validated);

        return response()->json(['data' => $serviceConnection->fresh()]);
    }

    public function destroy(ServiceConnection $serviceConnection): JsonResponse
    {
        $this->authorize('delete', $serviceConnection);

        $serviceConnection->delete();

        return response()->json(null, 204);
    }
}

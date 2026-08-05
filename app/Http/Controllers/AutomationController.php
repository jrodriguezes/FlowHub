<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Automation::class);

        $automations = $request->user()
            ->automations()
            ->latest()
            ->get();

        return response()->json(['data' => $automations]);
    }

    public function show(Automation $automation): JsonResponse
    {
        $this->authorize('view', $automation);

        return response()->json(['data' => $automation]);
    }

    public function update(Request $request, Automation $automation): JsonResponse
    {
        $this->authorize('update', $automation);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $automation->update($validated);

        return response()->json(['data' => $automation->fresh()]);
    }

    public function destroy(Automation $automation): JsonResponse
    {
        $this->authorize('delete', $automation);

        $automation->delete();

        return response()->json(null, 204);
    }
}

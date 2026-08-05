<?php

namespace App\Http\Controllers;

use App\Models\AutomationExecution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationExecutionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AutomationExecution::class);

        $executions = $request->user()
            ->automationExecutions()
            ->latest()
            ->get();

        return response()->json(['data' => $executions]);
    }

    public function show(AutomationExecution $automationExecution): JsonResponse
    {
        $this->authorize('view', $automationExecution);

        return response()->json(['data' => $automationExecution]);
    }

    public function update(Request $request, AutomationExecution $automationExecution): JsonResponse
    {
        $this->authorize('update', $automationExecution);

        $validated = $request->validate([
            'status' => ['sometimes', 'string'],
        ]);

        $automationExecution->update($validated);

        return response()->json(['data' => $automationExecution->fresh()]);
    }

    public function destroy(AutomationExecution $automationExecution): JsonResponse
    {
        $this->authorize('delete', $automationExecution);

        $automationExecution->delete();

        return response()->json(null, 204);
    }
}

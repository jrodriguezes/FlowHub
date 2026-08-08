<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAutomationRequest;
use Illuminate\Support\Facades\DB;

class AutomationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Automation::class);

        $automations = $request->user()
            ->automations()
            ->latest()
            ->get();

        return view('automations.index', ['automations' => $automations]);
    }

    public function store(StoreAutomationRequest $request)
    {
        $this->authorize('store', Automation::class);

        DB::transaction(function () use ($request) {
            $automation = $request->user()->automations()->create($request->validated());
            $trigger = $request->trigger;
            $trigger = $automation->trigger()->create($trigger);
            if ($request->has('conditions') && is_array($request->conditions)) {
                foreach ($request->conditions as $index => $condition) {
                    $condition['position'] = $index;
                    $automation->conditions()->create($condition);
                }
            }
            if ($request->has('actions') && is_array($request->actions)) {
                foreach ($request->actions as $index => $action) {
                    $action['position'] = $index;
                    $automation->actions()->create($action);
                }
            }        
        });

        return redirect()->route('automations.index');
    }

    public function show(Automation $automation): View
    {
        $this->authorize('view', $automation);

        $automation->load(['trigger', 'conditions', 'actions']);

        return view('automations.show', compact('automation'));
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

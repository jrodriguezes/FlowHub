<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAutomationRequest;
use App\Models\Automation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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
            ->with(['trigger', 'conditions', 'actions'])
            ->latest()
            ->get();

        $connections = $request->user()->serviceConnections()->get();

        return view('automations.index', [
            'automations' => $automations,
            'connections' => $connections,
        ]);
    }

    public function store(StoreAutomationRequest $request): RedirectResponse
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

    public function update(UpdateAutomationRequest $request, Automation $automation): RedirectResponse
    {
        $this->authorize('update', $automation);

        DB::transaction(function () use ($request, $automation) {
            $automation->update($request->validated());
            $trigger = $request->trigger;
            $trigger = $automation->trigger()->updateOrCreate(
                ['id' => $automation->trigger?->id],
                $trigger
            );

            $automation->conditions()->delete();
            if ($request->has('conditions') && is_array($request->conditions)) {
                foreach ($request->conditions as $index => $condition) {
                    $condition['position'] = $index;
                    $automation->conditions()->create($condition);
                } 
            }

            $automation->actions()->delete();
            if ($request->has('actions') && is_array($request->actions)) {
                foreach ($request->actions as $index => $action) {
                    $action['position'] = $index;
                    $automation->actions()->create($action);
                }
            }
        });
        return redirect()->route('automations.index');
    }

    public function toggle(Automation $automation): RedirectResponse
    {
        $this->authorize('update', $automation);

        if (!$automation->is_active) {
            // Verificacion basica: Debe tener al menos un disparador y una accion
            if (!$automation->trigger || $automation->actions()->count() === 0) {
                return redirect()->route('automations.index')
                    ->with('error', 'No puedes activar una automatización sin disparador o sin acciones.');
            }
        }

        $automation->update([
            'is_active' => !$automation->is_active,
        ]);

        return redirect()->route('automations.index');
    }

    public function destroy(Automation $automation): RedirectResponse
    {
        $this->authorize('delete', $automation);

        $automation->delete();

        return redirect()->route('automations.index');
    }
}

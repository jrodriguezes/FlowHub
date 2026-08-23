<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\AutomationExecution;
use App\Services\ExecutionPayloadSanitizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutomationExecutionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AutomationExecution::class);

        $query = $request->user()
            ->automationExecutions()
            ->with('automation');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('automation_id')) {
            $query->where('automation_id', $request->integer('automation_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->string('from')->toString());
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->string('to')->toString());
        }

        $executions = $query->latest()->paginate(15)->withQueryString();

        $automations = $request->user()
            ->automations()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('executions.index', compact('executions', 'automations'));
    }

    public function show(AutomationExecution $automationExecution, ExecutionPayloadSanitizer $sanitizer): View
    {
        $this->authorize('view', $automationExecution);

        $automationExecution->load(['automation', 'steps.action']);

        return view('executions.show', [
            'execution' => $automationExecution,
            'sanitizedInput' => $sanitizer->sanitize($automationExecution->input_payload ?? []),
            'sanitizedOutput' => $sanitizer->sanitize($automationExecution->output_payload ?? []),
        ]);
    }
}

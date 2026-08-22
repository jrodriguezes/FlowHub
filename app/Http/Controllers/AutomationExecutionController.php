<?php

namespace App\Http\Controllers;

use App\Models\AutomationExecution;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutomationExecutionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AutomationExecution::class);

        // we retrieve the executions along with their parent, automation and paginate them in increments of 15
        $executions = $request->user()
            ->automationExecutions()
            ->with('automation')
            ->latest()
            ->paginate(15);
        return view('executions.index', compact('executions'));
    }

    public function show(AutomationExecution $automationExecution): View
    {
        $this->authorize('view', $automationExecution);
        // we bring the details to paint the Timeline
        $automationExecution->load(['automation', 'steps']);
        return view('executions.show', ['execution' => $automationExecution]);
    }
}

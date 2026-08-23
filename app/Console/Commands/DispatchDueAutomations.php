<?php

namespace App\Console\Commands;

use App\Services\ScheduledAutomationDispatcher;
use Illuminate\Console\Command;

class DispatchDueAutomations extends Command
{
    protected $signature = 'automations:dispatch-due';

    protected $description = 'Encola automatizaciones programadas cuya next_run_at ya venció';

    public function handle(ScheduledAutomationDispatcher $dispatcher): int
    {
        $count = $dispatcher->dispatchDue();

        $this->info("Automatizaciones encoladas: {$count}");

        return self::SUCCESS;
    }
}

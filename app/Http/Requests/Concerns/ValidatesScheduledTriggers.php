<?php

namespace App\Http\Requests\Concerns;

use App\Services\ScheduledTriggerPlanner;

trait ValidatesScheduledTriggers
{
    protected function validateScheduledTrigger($validator): void
    {
        $validator->after(function ($validator) {
            if (($this->input('trigger.type') ?? null) !== 'schedule') {
                return;
            }

            $cron = trim((string) $this->input('trigger.cron_expression', ''));

            if ($cron === '') {
                $validator->errors()->add(
                    'trigger.cron_expression',
                    'La expresión cron es obligatoria para triggers programados.',
                );

                return;
            }

            if (!app(ScheduledTriggerPlanner::class)->isValidExpression($cron)) {
                $validator->errors()->add(
                    'trigger.cron_expression',
                    'La expresión cron no es válida.',
                );
            }

            $timezone = trim((string) $this->input('trigger.timezone', ''));

            if ($timezone === '') {
                $validator->errors()->add(
                    'trigger.timezone',
                    'La zona horaria es obligatoria para triggers programados.',
                );
            } elseif (!in_array($timezone, timezone_identifiers_list(), true)) {
                $validator->errors()->add(
                    'trigger.timezone',
                    'La zona horaria no es válida.',
                );
            }
        });
    }
}

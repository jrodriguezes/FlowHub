<?php

namespace App\Services;

use Carbon\Carbon;
use Cron\CronExpression;
use InvalidArgumentException;

class ScheduledTriggerPlanner
{
    public function isValidExpression(string $expression): bool
    {
        return CronExpression::isValidExpression($expression);
    }

    public function assertValidExpression(string $expression): void
    {
        if (!$this->isValidExpression($expression)) {
            throw new InvalidArgumentException('Expresión cron inválida.');
        }
    }

    public function computeNextRunAt(string $cronExpression, string $timezone, ?Carbon $from = null): Carbon
    {
        $this->assertValidExpression($cronExpression);

        $cron = new CronExpression($cronExpression);
        $from ??= now($timezone);

        $fromLocal = $from->copy()->timezone($timezone);

        $next = $cron->getNextRunDate(
            $fromLocal->toDateTime(),
            0,
            false,
        );

        return Carbon::instance($next)->utc();
    }
}

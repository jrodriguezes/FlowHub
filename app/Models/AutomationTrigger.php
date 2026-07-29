<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationTrigger extends Model
{
    protected $fillable = ['automation_id', 'type', 'provider', 'config', 'cron_expression', 'timezone', 'next_run_at', 'webhook_secret'];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'next_run_at' => 'datetime',
            'webhook_secret' => 'encrypted', // Protegemos el secreto (RA-04)
        ];
    }

}

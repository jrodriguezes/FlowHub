<?php

namespace App\Models;

use App\Enums\ExecutionStatus;
use Database\Factories\AutomationExecutionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationExecution extends Model
{
    /** @use HasFactory<AutomationExecutionFactory> */
    use HasFactory;

    protected $fillable = [
        'automation_id',
        'user_id',
        'event_key',
        'status',
        'started_at',
        'completed_at',
        'input_payload',
        'output_payload',
        'error_details',
    ];

    protected function casts(): array
    {
        return [
            'status' => ExecutionStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'input_payload' => 'array',
            'output_payload' => 'array',
            'error_details' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function automation()
    {
        return $this->belongsTo(Automation::class);
    }

    public function steps()
    {
        return $this->hasMany(ExecutionStep::class)->orderBy('position');
    }
}

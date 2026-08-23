<?php

namespace App\Models;

use App\Enums\ExecutionStatus;
use Illuminate\Database\Eloquent\Model;

class ExecutionStep extends Model
{

    protected $fillable = ['automation_execution_id', 'automation_action_id', 'position', 'status', 'attempts', 'started_at', 'completed_at', 'input_payload', 'output_payload', 'error_details'];

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

    public function execution()
    {
        return $this->belongsTo(AutomationExecution::class, 'automation_execution_id');
    }

    public function action()
    {
        return $this->belongsTo(AutomationAction::class, 'automation_action_id');
    }
}

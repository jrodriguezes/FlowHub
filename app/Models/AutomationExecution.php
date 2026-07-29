<?php

namespace App\Models;

use App\Enums\ExecutionStatus;
use Illuminate\Database\Eloquent\Model;

class AutomationExecution extends Model
{
    protected $fillable = ['automation_id', 'user_id', 'event_key', 'status', 'input_payload', 'output_payload', 'error_details'];

    protected function casts(): array
    {
        return [
            'status' => ExecutionStatus::class,
            'input_payload' => 'array',
            'output_payload' => 'array',
            'error_details' => 'array',
        ];
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

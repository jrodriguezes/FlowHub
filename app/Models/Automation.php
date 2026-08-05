<?php

namespace App\Models;

use Database\Factories\AutomationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Automation extends Model
{
    /** @use HasFactory<AutomationFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trigger()
    {
        return $this->hasOne(AutomationTrigger::class);
    }

    public function conditions()
    {
        return $this->hasMany(AutomationCondition::class)->orderBy('position');
    }

    public function actions()
    {
        return $this->hasMany(AutomationAction::class)->orderBy('position');
    }

    public function executions()
    {
        return $this->hasMany(AutomationExecution::class);
    }
}

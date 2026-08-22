<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationAction extends Model
{
    protected $fillable = ['automation_id', 'service_connection_id', 'type', 'position', 'config'];

    protected function casts(): array
    {
        return ['config' => 'array'];
    }

    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'service_connection_id');
    }
}

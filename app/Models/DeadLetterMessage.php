<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeadLetterMessage extends Model
{
    protected $fillable = ['queue', 'payload', 'exception', 'failed_at'];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'failed_at' => 'datetime',
        ];
    }

}

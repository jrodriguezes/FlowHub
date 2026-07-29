<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationCondition extends Model
{
    protected $fillable = ['automation_id', 'position', 'field', 'operator', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array', // Para manejar el JSONB desde PHP como un arreglo
        ];
    }

}

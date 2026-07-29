<?php

namespace App\Models;

use App\Enums\ConnectionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'external_id',
        'access_token',
        'refresh_token',
        'scopes',
        'expires_at',
        'status',
        'revoked_at'
    ];

    protected function casts(): array
    {
        return [
            // cifrado (RA-04)
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',

            // Convertimos el JSONB de Postgres a un Array de PHP
            'scopes' => 'array',

            // Fechas
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',

            // Vinculamos el campo con el Enum
            'status' => ConnectionStatus::class,
        ];
    }
    // Una conexión pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


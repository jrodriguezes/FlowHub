<?php

namespace App\Enums;

enum ConnectionStatus: string
{
    case ACTIVE = 'active';
    case REVOKED = 'revoked';
}

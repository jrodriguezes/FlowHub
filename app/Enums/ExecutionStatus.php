<?php

namespace App\Enums;

enum ExecutionStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESSFUL = 'successful';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';

}

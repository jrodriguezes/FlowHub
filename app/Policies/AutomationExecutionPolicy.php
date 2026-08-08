<?php

namespace App\Policies;

use App\Models\AutomationExecution;
use App\Models\User;

class AutomationExecutionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AutomationExecution $automationExecution): bool
    {
        return $user->id === $automationExecution->user_id;
    }

    public function store(User $user): bool
    {
        return true;
    }

    public function update(User $user, AutomationExecution $automationExecution): bool
    {
        return $user->id === $automationExecution->user_id;
    }

    public function delete(User $user, AutomationExecution $automationExecution): bool
    {
        return $user->id === $automationExecution->user_id;
    }
}

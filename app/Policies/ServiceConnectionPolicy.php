<?php

namespace App\Policies;

use App\Models\ServiceConnection;
use App\Models\User;

class ServiceConnectionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ServiceConnection $serviceConnection): bool
    {
        return $user->id === $serviceConnection->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ServiceConnection $serviceConnection): bool
    {
        return $user->id === $serviceConnection->user_id;
    }

    public function delete(User $user, ServiceConnection $serviceConnection): bool
    {
        return $user->id === $serviceConnection->user_id;
    }
}

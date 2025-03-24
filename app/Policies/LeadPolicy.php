<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function view(User $user, Lead $lead): bool
    {
        return $user->isAdmin() || $user->id === $lead->user_id;
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create leads
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->isAdmin() || $user->id === $lead->user_id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->isAdmin() || $user->id === $lead->user_id;
    }

    public function share(User $user, Lead $lead): bool
    {
        return $user->isAdmin() || $user->id === $lead->user_id;
    }
} 
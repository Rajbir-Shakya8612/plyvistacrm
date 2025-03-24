<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function view(User $user, Sale $sale): bool
    {
        return $user->isAdmin() || $user->id === $sale->user_id;
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create sales
    }

    public function update(User $user, Sale $sale): bool
    {
        return $user->isAdmin() || $user->id === $sale->user_id;
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $user->isAdmin() || $user->id === $sale->user_id;
    }
} 
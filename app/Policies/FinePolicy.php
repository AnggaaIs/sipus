<?php

namespace App\Policies;

use App\Models\Fine;
use App\Models\User;

class FinePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->isActiveAdmin($user) || $user->canBorrowBooks();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fine $fine): bool
    {
        return $this->isActiveAdmin($user) || $fine->user_id === $user->getKey();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fine $fine): bool
    {
        return $this->isActiveAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fine $fine): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fine $fine): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fine $fine): bool
    {
        return false;
    }

    private function isActiveAdmin(User $user): bool
    {
        return $user->isAdmin() && $user->isApproved() && $user->is_active;
    }
}

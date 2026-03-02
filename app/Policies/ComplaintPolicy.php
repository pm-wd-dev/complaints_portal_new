<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ComplaintPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view complaints list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Complaint $complaint): bool
    {
        // Admin can view all complaints
        if ($user->role === 'admin') {
            return true;
        }

        // Users can only view their own complaints
        return $user->id === $complaint->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create complaints
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Complaint $complaint): bool
    {
        // Admin can update any complaint
        if ($user->role === 'admin') {
            return true;
        }

        // Users can only update their own complaints if they're still open
        return $user->id === $complaint->user_id && $complaint->status === 'open';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Complaint $complaint): bool
    {
        // Only admins can delete complaints
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Complaint $complaint): bool
    {
        // Only admins can restore complaints
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Complaint $complaint): bool
    {
        // Only admins can force delete complaints
        return $user->role === 'admin';
    }
}

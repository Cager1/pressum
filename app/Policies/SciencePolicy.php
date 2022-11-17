<?php

namespace App\Policies;

use App\Models\Science;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SciencePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Science  $science
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Science $science)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // check if user role has permission to create science
        return $user->role->permissions->contains('name', 'create_science');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Science  $science
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Science $science)
    {
        return $user->role->permissions->contains('name', 'update_science');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Science  $science
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Science $science)
    {
        return $user->role->permissions->contains('name', 'delete_science');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Science  $science
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Science $science)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Science  $science
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Science $science)
    {
        //
    }
}

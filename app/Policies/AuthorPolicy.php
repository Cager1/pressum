<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthorPolicy
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
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Author $author)
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
        // check if user role has permission to create author
        return $user->role->permissions->contains('name', 'create_author');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Author $author)
    {
        // check if user role has permission to update author
        return $user->role->permissions->contains('name', 'update_author');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Author $author)
    {
        // check if user role has permission to delete author
        return $user->role->permissions->contains('name', 'delete_author');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Author $author)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Author $author)
    {
        //
    }
}

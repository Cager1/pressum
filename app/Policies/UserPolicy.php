<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use phpDocumentor\Reflection\Types\True_;

class UserPolicy
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
        // return true if user has permission to view any user
        return $user->role->permissions->contains('name', 'view_user');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        // return true if user has permission to view any user or is viewing his own profile
        return $user->role->permissions->contains('name', 'view_user') || $user->uid == $model->uid;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        // return true if user has permission to update any user or is updating his own profile
        return $user->role->permissions->contains('name', 'update_user') || $user->uid == $model->uid;
    }

    // Determine whether the user can detach a book from user
    public function detachBook(User $user, Book $book)
    {
        // return true if user has permission to detach book from any user or is detaching book from his own profile
        return $user->role->permissions->contains('name', 'detach_book') || $user->uid == $book->created_by;
    }


}

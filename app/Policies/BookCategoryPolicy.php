<?php

namespace App\Policies;

use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookCategoryPolicy
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
     * @param  \App\Models\Book  $bookCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Book $bookCategory)
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
        // user role permission has create_bookCategory
        return $user->role->permissions->contains('name', 'create_bookCategory');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Book  $bookCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Book $bookCategory)
    {
        // check if user role has permission to update bookCategory and if user is the owner
        return $user->role->permissions->contains('name', 'update_bookCategory') || $user->uid == $bookCategory->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Book  $bookCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Book $bookCategory)
    {

        // check if user role has permission to delete bookCategory or if user is the owner
        return $user->role->permissions->contains('name', 'delete_bookCategory') || $user->uid === $bookCategory->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Book  $bookCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */

    public function purchase(User $user, Book $bookCategory)
    {
        // if bookCategory is not public
        return true;
    }
}

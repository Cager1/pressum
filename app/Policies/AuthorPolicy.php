<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\Book;
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
        // if user contains permission to update author
        if ($user->role->permissions->contains('name', 'update_author')) {
            return true;
        } else {
            // else if author has user attached
            if ($author->user_uid !== null) {
                // if true check if user is the owner
                return $author->user_uid == $user->uid;
            // else if author is creatred by user
            } else return $author->created_by == $user->uid;
        }
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
        if ($user->role->permissions->contains('name', 'delete_author')) {
            return true;
        } else {
            if ($author->user_uid !== null) {
                return $author->user_uid == $user->uid;
            } else return $author->created_by == $user->uid;
        }
    }

  // Determine whether the user can detach a book
    public function detachBook(User $user, Author $author, Book $book)
    {
        // If user has permission to detach book
        // or if user is the owner of the author and book is attached to author
        return $user->role->permissions->contains('name', 'detach_book') || ($author->user_uid == $user->uid && $author->books->contains($book));
    }

}

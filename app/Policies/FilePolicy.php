<?php

namespace App\Policies;

use App\Models\ResourceFile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class FilePolicy
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
        // only if user role has permission for all
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceFile  $resourceFile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ResourceFile $resourceFile)
    {
        // If user role has permission for all
        // If file is image, allow.
        // If file is pdf, allow if file is cut version else check if user has permission or if he owns it
        // If user purchased the file, allow
        // If book of file is not locked, allow
        // If book of file is not cut version, allow
        error_log($resourceFile->book->locked);

        return Str::contains($resourceFile->mimetype, 'image') || $resourceFile->cut_version
            || $user->role?->permissions->contains('name', 'view_file') || $user->purchases->contains('id', $resourceFile->book->id)
            || $user->uid == $resourceFile->book->created_by
            || (!$resourceFile->book->cut_version && !$resourceFile->book->locked);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // check if user role has permission to create file
        return $user->role->permissions->contains('name', 'create_file');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceFile  $resourceFile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ResourceFile $resourceFile)
    {
        // if user has permission to update_file
        // if user owns the file
        return $user->role->permissions->contains('name', 'update_file') || $user->uid == $resourceFile->book->user_uid;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceFile  $resourceFile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ResourceFile $resourceFile)
    {
        // if user has permission to delete_file
        // if user owns the file
        return $user->role->permissions->contains('name', 'delete_file') || $user->uid == $resourceFile->book->user_uid;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceFile  $resourceFile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ResourceFile $resourceFile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceFile  $resourceFile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ResourceFile $resourceFile)
    {
        //
    }
}

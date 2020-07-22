<?php

namespace App\Policies;

use App\Library;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return mixed
     */
    public function view(User $user, Library $library)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the library members.
     *
     * @param User $user
     * @param Library $library
     * @return bool
     */
    public function members(User $user, Library $library)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return mixed
     */
    public function update(User $user, Library $library)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return mixed
     */
    public function delete(User $user, Library $library)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return mixed
     */
    public function restore(User $user, Library $library)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return mixed
     */
    public function forceDelete(User $user, Library $library)
    {
        return $user->isAdmin();
    }
}

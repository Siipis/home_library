<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $user)
    {
        return $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function invite(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target)
    {
        return $user->id === $target->id || $user->isAdmin();
    }

    public function promote(User $user, User $target)
    {
        return $user->id != $target->id && $user->isAdmin();
    }

    public function delete(User $user, User $target)
    {
        return $user->id != $target->id && $user->isAdmin();
    }
}
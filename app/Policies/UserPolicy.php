<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $user, User $signedInUser)
    {
        return $user->id === $signedInUser->id;
    }

    public function admin(User $user, User $signedInUser)
    {
        return $signedInUser->isAdmin();
    }
}

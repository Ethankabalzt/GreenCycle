<?php

namespace App\Policies;

use App\Models\Tree;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TreePolicy
{
    public function care(User $user, Tree $tree): bool
    {
        return $user->id === $tree->user_id;
    }
}

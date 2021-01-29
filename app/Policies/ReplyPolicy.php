<?php

namespace App\Policies;

use App\Repositories\Models\Reply;
use App\Repositories\Models\User;

class ReplyPolicy extends Policy
{
    public function destroy(User $user, Reply $reply)
    {
        return $user->isAuthorOf($reply) || $user->isAuthorOf($reply->topic);
    }
}

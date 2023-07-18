<?php

namespace App\Trait;

trait AdminTrait
{
    public function isAdmin()
    {
        $user = auth()->user();
        if ($user->account_type == 'admin' && $user->role_id != null) {
            return true;
        }
        if ($user->account_type == 'teacher') {
            $profile = get_profile($user);
            if ($profile['can_add_videos'] == 1) {
                return true;
            }
        }

        return false;
    }
}

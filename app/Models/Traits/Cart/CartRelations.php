<?php

namespace App\Models\Traits\Cart;

use App\Models\User;
use App\Models\Video;

trait CartRelations
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}

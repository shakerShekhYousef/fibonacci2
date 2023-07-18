<?php

namespace App\Trait;

trait ResponseTrait
{
    public function unAuthenticated()
    {
        return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
    }
}

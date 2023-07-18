<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;

class CanAddVideos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user->account_type == 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher != null && $teacher->can_add_videos == 1) {
                return $next($request);
            }
        }

        return forbidden_response('forbidden');
    }
}

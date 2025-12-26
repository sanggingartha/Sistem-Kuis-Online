<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $path = $request->path();

        if ($user->role === 'pengajar') {
            if (!str_starts_with($path, 'pengajar')) {
                abort(404);
            }
        }
        if ($user->role === 'siswa') {
            if (str_starts_with($path, 'pengajar')) {
                abort(404);
            }
        }

        return $next($request);
    }
}

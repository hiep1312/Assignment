<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()){
            if(request()->routeIs('admin.*')) return redirect()->route('auth.admin.login');
            else return redirect()->route('auth.admin.login');
        }

        $user = Auth::user();

        if (in_array($user->role->value, $roles)) return $next($request);
        abort(404);
    }
}

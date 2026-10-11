<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeePasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('employee')->check()) {
            $user = auth()->guard('employee')->user();
            
            // Allow them to visit the reset password routes specifically
            if ($user->is_first_login && ! $request->routeIs('employee.password.reset') && ! $request->routeIs('employee.password.update') && ! $request->routeIs('employee.logout')) {
                return redirect()->route('employee.password.reset');
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsTeamleader
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $employee = auth()->guard('employee')->user()?->employee;

        if (!$employee || !$employee->is_teamleader) {
            abort(403, 'Unauthorized action. Teamleader access required.');
        }

        return $next($request);
    }
}

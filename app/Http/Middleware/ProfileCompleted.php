<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->guard('employee')->user();
        
        if ($user && $user->employee && !$user->employee->is_profile_completed) {
            
            // Allow dashboard and complete profile routes
            if ($request->routeIs('employee.dashboard') || 
                $request->routeIs('employee.complete-profile*') || 
                $request->routeIs('employee.logout')) {
                return $next($request);
            }

            if ($request->isMethod('get')) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Please complete your profile to continue.',
                        'requires_profile_completion' => true
                    ], 403);
                }
                // Return a view that just opens the modal
                return response()->view('employee.portal.require_profile');
            }

            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Please complete your profile to continue.',
                        'requires_profile_completion' => true
                    ], 403);
                }
                
                return redirect()->back()->with('error', 'Please complete your profile to continue.')->with('requires_profile_completion', true);
            }
        }

        return $next($request);
    }
}

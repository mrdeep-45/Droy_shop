<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompanyIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $session_id = $request->session()->get('company_id') ?? $request->session()->get('staff_id') ?? $request->session()->get('manager_id');

        if (!$session_id) {
            return redirect('/');
        }
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}

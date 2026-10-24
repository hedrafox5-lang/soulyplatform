<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffOrAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('citadel_toast', [
                'type' => 'error',
                'message' => 'يرجى تسجيل الدخول أولاً للوصول إلى مركز القيادة.'
            ]);
        }

        if (!in_array(auth()->user()->role, ['admin', 'staff'])) {
            abort(403, 'غير مصرح لك بالوصول إلى مركز القيادة والعمليات.');
        }

        return $next($request);
    }
}

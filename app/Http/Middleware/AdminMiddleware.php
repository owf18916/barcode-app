<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin_logged_in')) {
            // Jangan redirect lagi kalau sudah di halaman login
            if ($request->routeIs('admin.login')) {
                return $next($request);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}


<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Simple password-based auth for demo/small project
        // You can set ADMIN_PASSWORD in your .env
        $password = env('ADMIN_PASSWORD', 'admin123');

        if ($request->header('Authorization') !== 'Basic ' . base64_encode("admin:$password")) {
            return response('Admin Access Required', 401, [
                'WWW-Authenticate' => 'Basic realm="Admin Access"'
            ]);
        }

        return $next($request);
    }
}

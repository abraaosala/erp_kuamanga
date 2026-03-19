<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return redirect('/login');
        }

        return $next($request);
    }
}

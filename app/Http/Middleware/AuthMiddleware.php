<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthMiddleware
{
    /**
     * @param \Closure(Request): (Response|RedirectResponse) $next
     */
    public function handle(Request $request, \Closure $next): Response|RedirectResponse
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return redirect('/login');
        }

        return $next($request);
    }
}

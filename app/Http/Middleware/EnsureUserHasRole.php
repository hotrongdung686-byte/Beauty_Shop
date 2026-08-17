<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Allow the request through only if the authenticated user has one of the
     * given roles (e.g. `role:manager` or `role:manager,staff`).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Bạn không có quyền truy cập khu vực này.');
        }

        if (! $user->is_active) {
            abort(403, 'Tài khoản của bạn đã bị khóa.');
        }

        return $next($request);
    }
}

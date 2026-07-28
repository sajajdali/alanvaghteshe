<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user() && $request->user()->can('ADMIN_ACCESS')) {
            return $next($request);
        }
        if ($request->user()) {
            return $next($request);
        }
        //send 404
        abort(404);
    }
}

<?php

namespace Madewithlove\FeatureFlags\Http\Middleware;

use Closure;
use Madewithlove\FeatureFlags\FeatureFlags;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return FeatureFlags::check($request) ? $next($request) : abort(403);
    }
}

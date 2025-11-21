<?php

namespace Gecche\Cupparis\App\Http\Middleware;

use Illuminate\Support\Str;

class ProcessAuthBT
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$attributes)
    {
        $authBT = $request->header('AuthBT');
        if (!$authBT || !Str::contains($authBT,"Bearer")) {
            return $next($request);
        }
        $authorization = $request->header('Authorization');
        if (Str::contains($authorization,"Bearer")) {
            return $next($request);
        }

        if (Str::length(trim($authorization)) > 0) {
            $authBT = ', ' . $authBT;
        }
        $authorization .= $authBT;

        $request->headers->set('Authorization',$authorization);

        return $next($request);

    }
}

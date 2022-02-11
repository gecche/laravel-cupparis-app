<?php

namespace Gecche\Cupparis\App\Http\Middleware;


use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageCacheController;

class IsSuperuser
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
        $user = Auth::user();

        if ($user && $user->hasRole('Superutente')) {
            return $next($request);
        }

        return redirect('/');

    }
}

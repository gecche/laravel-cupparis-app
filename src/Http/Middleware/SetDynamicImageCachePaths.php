<?php

namespace Gecche\Cupparis\App\Http\Middleware;


use Intervention\Image\ImageCacheController;

class SetDynamicImageCachePaths
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
        ImageCacheController::setDynamicPaths([storage_temp_path()]);


        return $next($request);
    }
}

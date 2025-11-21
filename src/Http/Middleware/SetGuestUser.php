<?php

namespace Gecche\Cupparis\App\Http\Middleware;


use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class SetGuestUser
{

    use GuestUserTrait;
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

        if (!$user) {
            $this->setGuestUser();
        }

        return $next($request);

    }
}

<?php

namespace Gecche\Cupparis\App\Http\Middleware;


use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class SetGuestUser
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

        if (!$user) {
            $user = new User();
            $roles = new Collection();
            $roles->add(Role::where('name',Config::get('cupparis-app.guest-role','Guest'))->first());
            $user->setRelation('roles',$roles);
            Auth::setUser($user);
        }

        return $next($request);

    }
}

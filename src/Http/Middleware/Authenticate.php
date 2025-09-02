<?php

namespace Gecche\Cupparis\App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class Authenticate extends Middleware
{

    use GuestUserTrait;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('dashboard');
        }
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            switch ($guard) {
                case 'sanctumguest':
                    if ($this->auth->guard('sanctum')->check()) {
                        return $this->auth->shouldUse('sanctum');
                    } else {
                        $this->setGuestUser();
                        return $this->auth->shouldUse('sanctum');
                    }
                default:
                    if ($this->auth->guard($guard)->check()) {
                        return $this->auth->shouldUse($guard);
                    }
                    break;
            }
        }

        $this->unauthenticated($request, $guards);
    }
}

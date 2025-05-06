<?php

namespace Gecche\Cupparis\App\Http\Middleware;

use App\Http\Controllers\JsonControllerTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\PersonalAccessToken;

class BTRoute
{

    use JsonControllerTrait;
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

        $bt = $request->get(Config::get('app.bearer_token_request_name','bt'));

        if (!$bt) {
            $this->_error("Bearer token not found");
            return response()->json($this->json, Response::HTTP_BAD_REQUEST);
        }

        $pat = PersonalAccessToken::findToken($bt);

        if (!$pat) {
            $this->_error("The bearer token does not match");
            return response()->json($this->json, Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}

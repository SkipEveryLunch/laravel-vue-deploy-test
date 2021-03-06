<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($req, Closure $next, ...$guards)
    {
        if($jwt = $req->cookie("jwt")){
            $req->headers->set(
                "Authorization",
                "Bearer ".$jwt
            );
        }
        $this->authenticate($req, $guards);

        return $next($req);
    }
    
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class AdminProtected
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
        if(session()->has('user')){
            if ((session()->get('user')['role_id'] == config('application.role_id')['admin_id']) || (session()->get('user')['role_id'] == config('application.role_id')['dev'])) {
               return $next($request);
            }else{
               die('you are not allowed here!!!');
            }
        }
        
    }
}

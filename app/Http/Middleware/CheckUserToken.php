<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckUserToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah token user ada di session
        $userToken = Session::get('user_token') ?? session('user_token');
        
        if (is_null($userToken)) {
            // Jika token tidak ada, arahkan ke halaman login
            return redirect('/login');
        }

        // Jika token ada, lanjutkan request
        return $next($request);
    }
}

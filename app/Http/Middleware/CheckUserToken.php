<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class CheckUserToken
{
    public function handle(Request $request, Closure $next)
    {
        $userToken = Session::get('user_token') ?? session('user_token');

        if (is_null($userToken)) {
            return redirect('/login');
        }

        $user = User::where('token', $userToken)->first();
        
        if (is_null($user)) {
            return redirect('/login')->with('error', 'Token Expired, silakan login kembali.');
        }

        return $next($request);
    }
}

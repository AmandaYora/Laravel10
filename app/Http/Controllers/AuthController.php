<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function index()
    {
        return view('content.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $response = $this->authLogin($username, $password);

        if ($response && $response->code === 0) {
            $roleData = $response->data->roles[0];
            session(['roles' => $roleData]);
            return redirect('/users')->with('success', 'Login berhasil');
        } else {
            return back()->withErrors(['message' => 'Login gagal, username atau password salah.']);
        }
    }

    public function logout()
    {
        // Menghapus semua data session
        session()->flush();
        
        // Redirect ke halaman login
        return redirect('/login')->with('success', 'Logout berhasil');
    }
}

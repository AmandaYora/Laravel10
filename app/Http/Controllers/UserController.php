<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function admin()
    {
        $admins = User::where('role_id', Role::ROLE_ADMIN)->get();
        return view('users.admin', ['users' => $admins]);
    }

    public function user()
    {
        $users = User::where('role_id', Role::ROLE_USER)->get();
        return view('users.users', ['users' => $users]);
    }


    public function saveAdmin(Request $request)
    {
        $admin = $request->input('user_id') ? User::find($request->input('user_id')) : new User;
        $admin->fill($request->all());
        $admin->role_id = Role::ROLE_ADMIN;

        if ($admin->save()) {
            return back()->with('success', 'Admin saved successfully.');
        } else {
            return back()->with('error', 'Failed to save admin.');
        }
    }

    public function saveUser(Request $request)
    {
        $user = $request->input('user_id') ? User::find($request->input('user_id')) : new User;
        $user->fill($request->all());
        $user->role_id = Role::ROLE_USER;

        if ($user->save()) {
            return back()->with('success', 'User saved successfully.');
        } else {
            return back()->with('error', 'Failed to save user.');
        }
    }

}

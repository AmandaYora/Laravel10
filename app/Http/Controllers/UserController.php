<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function user()
    {
        $users = User::where('role_id', Role::ROLE_USER)->get();
        return view('users.users', ['users' => $users]);
    }

    public function saveUser(Request $request)
    {
        $user = $request->input('user_id') ? User::find($request->input('user_id')) : new User;
        $user->fill($request->all());

        if ($user->save()) {
            return back()->with('success', 'User saved successfully.');
        } else {
            return back()->with('error', 'Failed to save user.');
        }
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return back()->with('success', 'User deleted successfully.');
        } else {
            return back()->with('error', 'User not found.');
        }
    }
}

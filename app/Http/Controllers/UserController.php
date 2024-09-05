<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Services\Person;

class UserController extends Controller
{
    public function user()
    {
        $users = $this->getAllUsers(Person::MODE_ID);

        return view('content.users.index', ['users' => $users->data]);
    }

    public function saveUser(Request $request)
    {
        $user = $request->input('user_id') ? User::find($request->input('user_id')) : new User;
    
        $data = $request->except(['token', 'nonce', 'password']);
    
        $user->fill($data);
    
        if ($request->filled('password')) {
            $user->password = $request->input('password');
        }
    
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

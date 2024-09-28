<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Services\Person;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function user()
    {
        $users = $this->getAllUsers(Person::MODE_ID)->data ?? [];
        $roles = Role::all();

        $data = [
            'users' => $users,
            'roles' => $roles,
        ];

        return view('content.users.index', $data);
    }

    public function saveUser(Request $request)
    {
        if ($request->input('user_id')) {
            $user = User::find($request->input('user_id'));
        } else {
            $user = User::withTrashed()->where('code', $request->input('code'))->first();
            
            if ($user) {
                $user->restore();
            } else {
                $user = new User;
            }

            $is_new = true;
        }
    
        $data = $request->except(['token', 'nonce', 'password']);
        
        if ($is_new) {
            $data['is_verify'] = 0;
        }

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

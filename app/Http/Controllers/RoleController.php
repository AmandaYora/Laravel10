<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleAccess;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('content.roles.index', ['roles' => $roles]);
    }

    public function saveRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($request->has('role_id') && !empty($request->input('role_id'))) {
            $role = Role::findOrFail($request->input('role_id'));
            $role->update($request->only(['role_name', 'description']));
            $message = 'Role berhasil diperbarui.';
        } else {
            Role::create($request->only(['role_name', 'description']));
            $message = 'Role berhasil dibuat.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function deleteRole($role_id)
    {
        $role = Role::findOrFail($role_id);
        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }

    public function updateRolesUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,role_id',
        ]);

        $userId = $request->input('user_id');
        $newRoles = $request->input('roles', []);

        $existingRoles = RoleAccess::where('user_id', $userId)->pluck('role_id')->toArray();

        $rolesToAdd = array_diff($newRoles, $existingRoles);

        $rolesToRemove = array_diff($existingRoles, $newRoles);

        foreach ($rolesToAdd as $roleId) {
            RoleAccess::create([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }

        if (!empty($rolesToRemove)) {
            RoleAccess::where('user_id', $userId)
                ->whereIn('role_id', $rolesToRemove)
                ->delete();
        }

        $user = User::find($userId);
        $user->is_verify = empty($rolesToAdd) ? 0 : 1;
        $user->save();

        return redirect()->back()->with('success', 'Role access updated successfully!');
    }
}

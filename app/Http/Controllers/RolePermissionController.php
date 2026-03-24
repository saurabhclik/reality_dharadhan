<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    private $secret;

    public function __construct()
    {
        $this->secret = config('secrets.role_permission');
    }

    public function form($secret)
    {
        $decodedSecret = urldecode($secret);

        if ($decodedSecret !== $this->secret) {
            abort(403, 'Unauthorized access.');
        }

        $roles = DB::table('role_mst')->orderBy('id', 'desc')->get();
        $permissions = DB::table('permissions')->orderBy('id', 'desc')->get();

        return view('staff-management.role-permission', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'manager_rights' => 'required|boolean',
        ]);

        $roleName = strtolower(str_replace(' ', '_', $request->role_name));

        DB::table('role_mst')->insert([
            'role_name' => $roleName,
            'manager_rights' => $request->manager_rights,
            'created_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'manager_rights' => 'required|boolean',
        ]);

        $roleName = strtolower(str_replace(' ', '_', $request->role_name));

        DB::table('role_mst')->where('id', $id)->update([
            'role_name' => $roleName,
            'manager_rights' => $request->manager_rights,
            'updated_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function delete($id)
    {
        DB::table('role_mst')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'permission_name' => 'required|string|max:255',
            'role_id' => 'required|integer',
        ]);

        DB::table('permissions')->insert([
            'permission_name' => $request->permission_name,
            'role_id' => $request->role_id,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Permission added successfully.');
    }

    public function updatePermission(Request $request, $id)
    {
        $request->validate([
            'permission_name' => 'required|string|max:255',
            'role_id' => 'required|integer',
        ]);

        DB::table('permissions')->where('id', $id)->update([
            'permission_name' => $request->permission_name,
            'role_id' => $request->role_id,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Permission updated successfully.');
    }

    public function deletePermission($id)
    {
        DB::table('permissions')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}

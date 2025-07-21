<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:role-list|role-create|role-edit|role-delete'], ['only' => ['index']]);
        $this->middleware(['permission:role-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:role-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:role-delete'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all roles from the database
        $roles = Role::all();
        
        // Return the roles to a view
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all permissions from the database
        $permissions = Permission::all();
        // Return the create role view with permissions
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array',
        ]);
        // Create a new role instance
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permissions', []));
        // Redirect to the roles index with a success message
        $notification = [
            'message' => 'Role created successfully.',
            'alert-type' => 'success',
        ];
        return redirect()->route('roles.index')->with($notification);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::findOrFail($id);
        // Fetch all permissions from the database
        $permissions = Permission::all();
        // Return the edit role view with role and permissions
        return view('roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        // Fetch all permissions from the database
        $permissions = Permission::all();
        // Return the edit role view with role and permissions
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);
        // Find the role by ID
        $role = Role::findOrFail($id);
        // Update the role name
        $role->name = $request->input('name');
        $role->save();
        // Sync the role's permissions
        $role->syncPermissions($request->input('permissions', []));
        // Redirect to the roles index with a success message
        $notification = [
            'message' => 'Role updated successfully.',
            'alert-type' => 'success',
        ];
        return redirect()->route('roles.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the role by ID
        $role = Role::findOrFail($id);
        // Delete the role
        $role->delete();
        // Redirect to the roles index with a success message
        $notification = [
            'message' => 'Role deleted successfully.',
            'alert-type' => 'success',
        ];
        return redirect()->route('roles.index')->with($notification);
    }
}

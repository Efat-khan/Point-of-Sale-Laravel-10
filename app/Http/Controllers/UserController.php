<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Image;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user-list|user-create|user-edit|user-delete'], ['only' => ['index']]);
        $this->middleware(['permission:user-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:user-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:user-delete'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id = $request->get('users_filter', null);
        $filter_users = User::all();
        // Fetch all users from the database
        if($user_id) {
            // If a user ID is provided, filter users by that ID
            $users = User::where('id', $user_id)->get();
        } else {
            // Otherwise, fetch all users
            $users = User::all();
        }
        // Return the users to a view
        return view('users.index', compact('users','user_id','filter_users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);
        if ($request->file('profile_image')) {
            $image = $request->file('profile_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension(); // Ex. 34343.jpg 
            Image::make($image)->resize(200, 200)->save('upload/admin_images/' . $name_gen);
            $save_url = 'upload/admin_images/' . $name_gen;
        }
        // Create a new user instance
        $user = new User();
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->email_verified_at = Carbon::now(); // Set email verification date to now
        $user->profile_image = $save_url??'upload/no_image.jpg';
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $user->syncRoles($request->input('roles', [])); // Sync roles if provided
        $notification = array(
            'message' => 'User created successfully',
            'alert-type' => 'success'
        );
        // Redirect to the users index with a success message
        return redirect()->route('users.index')->with($notification);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
        // Return the edit view with the user data
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles = Role::all();
        // Find the user by ID
        $user = User::findOrFail($id);
        // Return the edit view with the user data
        return view('users.edit', compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        // Find the user by ID
        $user = User::findOrFail($id);
        // Update user details
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');

        if ($request->file('profile_image')) {
            // Delete old image if it exists
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }
            // Handle profile image upload
            $image = $request->file('profile_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(200, 200)->save('upload/admin_images/' . $name_gen);
            $user->profile_image = 'upload/admin_images/' . $name_gen;
        }

        if ($request->input('password')) {
            // Update password if provided
            $user->password = Hash::make($request->input('password'));
        }

        // Save the updated user
        $user->save();
        $user->syncRoles($request->input('roles', [])); // Sync roles if provided
        $notification = array(
            'message' => 'User updated successfully',
            'alert-type' => 'success'
        );
        
        // Redirect to the users index with a success message
        return redirect()->route('users.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
        
        // Delete the user's profile image if it exists
        if ($user->profile_image && file_exists(public_path($user->profile_image))) {
            unlink(public_path($user->profile_image));
        }

        // Delete the user
        $user->delete();

        $notification = array(
            'message' => 'User deleted successfully',
            'alert-type' => 'success'
        );
        
        // Redirect to the users index with a success message
        return redirect()->route('users.index')->with($notification);
    }
}

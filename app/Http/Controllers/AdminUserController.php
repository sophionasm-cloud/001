<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
    }



    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::with('role')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        
        $user = User::findOrFail($id);
        $user->update($request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]));
        return redirect('admin/users')->with('success', 'User updated');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('admin/users')->with('success', 'User deleted');
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users.
    |
    */

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['sometimes', 'string', 'in:customer,vendor'],
            'store_name'   => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $isVendor = ($data['account_type'] ?? '') === 'vendor';
        $roleName = $isVendor ? 'Vendor' : 'Customer';
        $roleId   = Role::where('name', $roleName)->value('id') ?? ($isVendor ? 2 : 3);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $roleId,
        ]);

        if ($isVendor) {
            \App\Models\Vendor::create([
                'user_id'         => $user->id,
                'store_name'      => !empty($data['store_name']) ? $data['store_name'] : $data['name'] . ' Store',
                'description'     => 'Vendor Store on MultiVendor Platform',
                'approval_status' => 'active',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended($user->homeRoute());
    }
}

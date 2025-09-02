<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

    public function edit()
    {
        return view('profile_edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'company_name' => 'nullable|string|max:255',
        'company_address' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->company_name = $request->company_name;
        $user->company_address = $request->company_address;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    public function editPassword()
    {
        return view('password_change');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:4|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect!']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Password updated successfully!');
    }
}

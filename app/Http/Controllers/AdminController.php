<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();

        if ($currentUser->role === 'superadmin') {
            $users = User::whereIn('role', ['admin', 'client'])->get(); // Superadmin sees admins and clients
        } elseif ($currentUser->role === 'admin') {
            $users = User::where('role', 'client')->get(); // Admin only sees clients
        } else {
            $users = User::where('role', 'client')->get(); // Clients see only other clients
        }

        return view('admin_accounts', compact('users'));
    }

    public function create()
    {
        $currentUser = auth()->user();

        // Superadmin & Admin can create users
        if (in_array($currentUser->role, ['superadmin', 'admin'])) {
            return view('create_user');
        }

        // Clients cannot access this
        return redirect()
            ->route('admin_accounts.index')
            ->with('error', 'You do not have permission to create users!');
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();

        // Superadmin can create all roles
        // Admin can only create clients
        $roleOptions = ($currentUser->role === 'superadmin')
            ? ['superadmin', 'admin', 'client']
            : ['client'];

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4|confirmed',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'role' => ['required', Rule::in($roleOptions)],
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'role' => $request->role,
        ]);

        return redirect()->route('admin_accounts.index')->with('success', 'User created successfully!');
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        // Rules:
        // Superadmin -> can delete anyone
        // Admin -> can delete only clients
        // Client -> cannot delete
        if ($currentUser->role === 'superadmin') {
            $user->delete();
        } elseif ($currentUser->role === 'admin' && $user->role === 'client') {
            $user->delete();
        } else {
            return redirect()->route('admin_accounts.index')->with('error', 'You do not have permission to delete this user!');
        }

        return redirect()->route('admin_accounts.index')->with('success', 'User deleted successfully!');
    }
}

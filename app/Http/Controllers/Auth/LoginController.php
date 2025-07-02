<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        $role = auth()->user()->role;

        \Log::info("Redirecting user with role: " . $role);

        switch ($role) {
            case 'superadmin':
                return '/superadmin/dashboard';
            case 'admin':
                return '/admin/dashboard';
            case 'client':
                return '/client/dashboard';
            default:
                return '/error';
        }
    }

    public function showLoginForm() 
    { 
        return view('auth.login'); 
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        return [
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ];
    }
}

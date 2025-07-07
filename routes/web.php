<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Landing page
Route::get('/', function () {
    if (auth()->check()) {
        switch (auth()->user()->username) {
            case 'superadmin':
                return redirect('/superadmin/dashboard');
            case 'admin':
                return redirect('/admin/dashboard');
            case 'client':
                return redirect('/client/dashboard');
            default:
                abort(403, 'Invalid username or misconfiguration');
        }
    }

    return view('welcome');
});

Route::get('/error', function () {
    return "Unknown username. Please contact admin.";
});


// Show login form
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Handle login POST
Route::post('/login', [LoginController::class, 'login']);

// Handle logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard routes
Route::middleware('auth')->get('/superadmin/dashboard', function () {
    return view('dashboards.superadmin');
})->name('superadmin.dashboard');

Route::middleware('auth')->get('/admin/dashboard', function () {
    return view('dashboards.admin');
})->name('admin.dashboard');

Route::middleware('auth')->get('/client/dashboard', function () {
    return view('dashboards.client');
})->name('client.dashboard');

// Reset session and logout (testing purposes)
Route::get('/reset-session', function () {
    Auth::logout();
    session()->flush();
    return redirect('/login');
});






Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'));
    Route::get('/profile', fn() => view('profile'));
    Route::get('/superadmin/manage', fn() => view('superadmin.manage'));
    Route::get('/admin/clients', fn() => view('admin.clients'));
});


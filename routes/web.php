<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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

Route::get('/', function () {
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
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
});

// Reset session and logout (testing purposes)
Route::get('/reset-session', function () {
    Auth::logout();
    session()->flush();
    return redirect('/login');
});






Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'));
    Route::get('/routers', fn() => view('routers'));
    Route::get('/networks', fn() => view('networks'));
    Route::get('/devices_in_networks', fn() => view('devices_in_networks'));
    Route::get('/standalone_vpn_clients', fn() => view('standalone_vpn_clients'));
    Route::get('/administration', fn() => view('administration'));
});

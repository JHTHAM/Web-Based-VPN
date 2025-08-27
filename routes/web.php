<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\NetworkController;

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

// First land on Login
Route::get('/', function () {
    return redirect()->route('login');
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


// Authentication routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'));
    Route::get('/routers', fn() => view('routers'));
    Route::get('/networks', fn() => view('networks'));
    Route::get('/devices_in_networks', fn() => view('devices_in_networks'));
    Route::get('/standalone_vpn_clients', fn() => view('standalone_vpn_clients'));
    Route::get('/administration', fn() => view('administration'));
});


// Router routes
Route::get('/routers', [RouterController::class, 'index'])->name('routers.index');           // Show router page
Route::post('/routers', [RouterController::class, 'store'])->name('routers.store');          // Create new router
Route::put('/routers/{id}', [RouterController::class, 'update'])->name('routers.update');    // Update router
Route::delete('/routers/{id}', [RouterController::class, 'destroy'])->name('routers.destroy'); // Delete router
Route::get('/routers/{id}/download', [RouterController::class, 'downloadOvpn'])->name('download.ovpn'); // Download .ovpn

Route::get('/dashboard', [RouterController::class, 'dashboard'])->name('dashboard');
Route::get('/routers/status', [RouterController::class, 'fetchStatus'])->name('routers.status');


// Network routes
Route::get('/networks', [NetworkController::class, 'index'])->name('networks.index');
Route::post('/networks', [NetworkController::class, 'store'])->name('networks.store');
Route::delete('/networks/{network}', [NetworkController::class, 'destroy'])->name('networks.destroy');

Route::get('/networks/{network}/devices', [NetworkController::class, 'devices'])->name('network.devices');
Route::post('/networks/{network}/devices', [NetworkController::class, 'addDevices'])->name('network.devices.store');
Route::delete('/networks/{network}/devices/{router}', [NetworkController::class, 'removeDevice'])->name('network.devices.destroy');

Route::get('/fetch-status', [NetworkController::class, 'fetchStatus']);


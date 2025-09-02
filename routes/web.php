<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\StandaloneClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

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
    Route::get('/devices', function () {
        return view('devices');
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
    Route::get('/devices', fn() => view('devices'));
    Route::get('/devices_in_networks', fn() => view('devices_in_networks'));
    Route::get('/routers', fn() => view('routers'));
    Route::get('/standalone_clients', fn() => view('standalone_clients'));
    Route::get('/networks', fn() => view('networks'));
    Route::get('/admin_accounts', fn() => view('admin_accounts'));
    Route::get('/client_accounts', fn() => view('client_accounts'));
    Route::get('/profile', fn() => view('profile'));
});


// Router routes
Route::get('/routers', [RouterController::class, 'index'])->name('routers.index');           // Show router page
Route::post('/routers', [RouterController::class, 'store'])->name('routers.store');          // Create new router
Route::put('/routers/{id}', [RouterController::class, 'update'])->name('routers.update');    // Update router
Route::delete('/routers/{id}', [RouterController::class, 'destroy'])->name('routers.destroy'); // Delete router
Route::get('/routers/{id}/download', [RouterController::class, 'downloadOvpn'])->name('router_download.ovpn'); // Download .ovpn

Route::get('/devices', [RouterController::class, 'dashboard'])->name('devices');
Route::get('/routers/status', [RouterController::class, 'fetchStatus'])->name('routers.status');


// Network routes
Route::get('/networks', [NetworkController::class, 'index'])->name('networks.index');
Route::post('/networks', [NetworkController::class, 'store'])->name('networks.store');
Route::delete('/networks/{network}', [NetworkController::class, 'destroy'])->name('networks.destroy');

Route::get('/networks/{network}/devices', [NetworkController::class, 'devices'])->name('network.devices');
Route::post('/networks/{network}/devices', [NetworkController::class, 'addDevices'])->name('network.devices.store');
Route::delete('/networks/{network}/devices/{router}', [NetworkController::class, 'removeDevice'])->name('network.devices.destroy');

Route::get('/fetch-status', [NetworkController::class, 'fetchStatus']);

Route::post('/networks/{network}/add-standalone', [NetworkController::class, 'addStandaloneDevices'])->name('network.standalone.store');
Route::delete('/networks/{network}/standalone/{client}', [NetworkController::class, 'removeStandaloneDevice'])->name('network.standalone.destroy');


// Standalone Client routes
Route::get('/standalone_clients', [StandaloneClientController::class, 'index'])->name('standalone_clients.index');
Route::post('/standalone_clients', [StandaloneClientController::class, 'store'])->name('standalone_clients.store');
Route::put('/standalone_clients/{id}', [StandaloneClientController::class, 'update'])->name('standalone_clients.update');
Route::delete('/standalone_clients/{id}', [StandaloneClientController::class, 'destroy'])->name('standalone_clients.destroy');
Route::get('/standalone_clients/{id}/download', [StandaloneClientController::class, 'downloadOvpn'])->name('client_download.ovpn');

Route::get('/standalone_clients/status', [StandaloneClientController::class, 'fetchStatus'])->name('standalone_clients.status'); // Fetch status using router.status


// Admin routes
Route::get('/admin_accounts', [AdminController::class, 'index'])->name('admin_accounts');
Route::post('/admin_accounts/users', [AdminController::class, 'store'])->name('admin_accounts.store');

// Profile routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');


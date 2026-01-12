<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ContractController;


Route::get('/', function () {
    // If user is already logged in, send them to the dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Otherwise, show the login view
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
   Route::resource('vendors', VendorController::class)->except(['create', 'edit', 'show']);
   Route::resource('contracts', ContractController::class)->except(['create', 'edit', 'show']);
});



require __DIR__.'/auth.php';

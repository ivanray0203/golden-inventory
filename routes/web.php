<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductClassController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('product-classes', ProductClassController::class);
});

require __DIR__.'/settings.php';

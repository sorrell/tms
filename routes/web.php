<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationInviteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('organizations', OrganizationController::class);

});


Route::middleware(['auth', 'verified', 'organization-assigned'])->group(function() {
    Route::get('dashboard', function() {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('organizations.invites', OrganizationInviteController::class)->scoped([
        'invite' => 'code',
    ]);

    Route::post('organizations/{organization}/invites/{invite:code}/resend', [OrganizationInviteController::class, 'resend'])->name('organizations.invites.resend');

});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationInviteController;
use App\Http\Controllers\PermissionController;
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

    Route::post('organizations/{organization}/invites/{invite:code}/accept', [OrganizationInviteController::class, 'accept'])->name('organizations.invites.accept');
    Route::resource('organizations.invites', OrganizationInviteController::class)->scoped([
        'invite' => 'code',
    ])->only(['show']);
});


Route::middleware(['auth', 'verified', 'organization-assigned'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('organizations.invites', OrganizationInviteController::class)->scoped([
        'invite' => 'code',
    ])->except(['show']);

    Route::delete('organizations/{organization}/users/{user}', [OrganizationController::class, 'removeUser'])->name('organizations.users.destroy');
    Route::post('organizations/{organization}/users/{user}/transfer', [OrganizationController::class, 'transferOwnership'])->name('organizations.users.transfer');

    Route::post('organizations/{organization}/invites/{invite:code}/resend', [OrganizationInviteController::class, 'resend'])->name('organizations.invites.resend');


    Route::prefix('organizations/{organization}/permissions')->group(function () {
        Route::post('assign-role-permission', [PermissionController::class, 'assignRolePermission'])->name('organizations.permissions.assign-role-permission');
        Route::post('remove-role-permission', [PermissionController::class, 'removeRolePermission'])->name('organizations.permissions.remove-role-permission');

        Route::post('assign-user-permission', [PermissionController::class, 'assignUserPermission'])->name('organizations.permissions.assign-user-permission');
        Route::post('remove-user-permission', [PermissionController::class, 'removeUserPermission'])->name('organizations.permissions.remove-user-permission');

        Route::post('assign-user-role', [PermissionController::class, 'assignUserRole'])->name('organizations.permissions.assign-user-role');
        Route::post('remove-user-role', [PermissionController::class, 'removeUserRole'])->name('organizations.permissions.remove-user-role');

        Route::post('role', [PermissionController::class, 'storeRole'])->name('organizations.permissions.store-role');
        Route::delete('role/{role}', [PermissionController::class, 'destroyRole'])->name('organizations.permissions.destroy-role');
        Route::patch('role/{role}', [PermissionController::class, 'updateRole'])->name('organizations.permissions.update-role');
    });
});

require __DIR__ . '/auth.php';

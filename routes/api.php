<?php

use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\PermissionUserController;
use App\Models\User;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/me', [AuthApiController::class, 'me'])->name('auth.me')->middleware('auth:sanctum');
Route::post('/logout', [AuthApiController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');
Route::post('/auth', [AuthApiController::class, 'auth'])->name('auth.login');

Route::middleware(['auth:sanctum', 'acl'])->group(function () {
    Route::apiResource('/permissions', PermissionController::class);

    Route::get('/users/{user}/permissions', [PermissionUserController::class, 'getPermissionsOfUser'])->name('users.permissions');
    Route::post('/users/{user}/permissions-sync', [PermissionUserController::class, 'syncPermissionsOfUser'])->name('users.permissions.sync');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.store');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

Route::get('/', fn () => response()->json(['message' => 'ok1']));

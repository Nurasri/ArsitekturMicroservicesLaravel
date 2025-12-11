<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// GET → Menampilkan semua user
Route::get('/users', [UserController::class, 'index']);

// POST → Menambah user baru
Route::post('/users', [UserController::class, 'store']);

// PUT → Update user
Route::put('/users/{id}', [UserController::class, 'update']);

// DELETE → Hapus user
Route::delete('/users/{id}', [UserController::class, 'destroy']);

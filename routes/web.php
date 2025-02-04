<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::group(['middleware' => 'auth'], function () {
    Route::post('books/{book}/file', [BookController::class, 'uploadFile'])->name('books.file');
    Route::post('advanced-search', [BookController::class, 'advancedSearch'])->name('books.advanced-search');
    Route::resource('books', BookController::class)->except(['create', 'edit']);
});

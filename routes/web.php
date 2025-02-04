<?php

use App\Http\Controllers\AuthController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/books');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::group(['middleware' => 'auth'], function () {
    Route::post('books/{book}/file', 'BookController@uploadFile')->name('books.file');
    Route::post('advanced-search', 'BookController@advancedSearch')->name('books.advanced-search');
    Route::resource('books', 'BookController')->except(['create', 'edit']);
});

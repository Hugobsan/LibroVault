<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('books/{book}/file', 'BookController@uploadFile')->name('books.file');
    Route::post('advanced-search', 'BookController@advancedSearch')->name('books.advanced-search');
    Route::resource('books', 'BookController')->except(['create', 'edit']);
});

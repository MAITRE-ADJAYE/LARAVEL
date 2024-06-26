<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
Route::delete('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('/login', [AuthController::class, 'doLogin']);

Route::prefix('/blog')->name('blog.')->controller(BlogController::class)->group(function () {
    Route::get('/','index')->name('index');
    Route::get('/new', 'create')->name('create')->middleware('auth');
    Route::post('/new', 'store')->middleware('auth');
    Route::get('/{post}/edit', 'edit')->name('edit')->middleware('auth');
    Route::patch('/{post}/edit', 'update')->middleware('auth'); // Utilisation de Route::patch au lieu de Route::pacth
    Route::get('/{slug}-{post}', 'show')->where([
        'post' => '[0-9]+',
        'slug' => '[a-z0-9\-]+'
    ])->name('show');
});




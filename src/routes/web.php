<?php

use App\Http\Controllers\UserController;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => '/admin', 'middleware' => [Authorize::using('publish articles')]], function () {
    Route::get('/user/create', [UserController::class, 'create']);

    Route::group(['prefix' => '/bi', 'middleware' => [Authorize::using('publish articles')]], function () {
        Route::get('/user/create', [UserController::class, 'create']);
    });
});

Route::group(['prefix' => '/driver', 'middleware' => [Authorize::using('publish articles')]], function () {
    Route::get('/user/create', [UserController::class, 'create']);
});



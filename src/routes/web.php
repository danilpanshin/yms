<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [UserController::class, 'login']);
Route::post('/login', [UserController::class, 'auth']);


Route::group(['middleware' => ['auth', 'throttle:6,1'], 'prefix' => '/driver'], function () {
    Route::get('/', [DriverController::class, 'index']);
}); // do not use middleware as continues callback


Route::group(['middleware' => ['auth', 'throttle:6,1'], 'prefix' => '/admin'], function () {
    Route::get('/', [AdminController::class, 'index']);
}); // do not use middleware as continues callback

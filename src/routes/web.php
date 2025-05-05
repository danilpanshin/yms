<?php

use App\Http\Controllers\UserController;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => '/admin'], function () {
    Route::get('/user', [UserController::class, 'index']);
})->middleware('can:is_authorized');

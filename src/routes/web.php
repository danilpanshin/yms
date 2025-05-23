<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDictionaryAcceptanceController;
use App\Http\Controllers\AdminDictionaryCarTypeController;
use App\Http\Controllers\AdminDictionaryGateController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


function crud_routes($class){
    Route::get('/', [$class, 'index'])->name('');
    Route::post('/add', [$class, 'add_post'])->name('.add_post');
    Route::post('/delete/{id}', [$class, 'delete_post'])->name('.delete_post')->where('id', '[0-9]+');
    Route::post('/edit/{id?}', [$class, 'edit_post'])->name('.edit_post')->where('id', '[0-9]+');
    Route::get('/one/{id?}', [$class, 'one'])->name('.one')->where('id', '[0-9]+');
}


Route::get('/', [IndexController::class, 'index'])->name('index');

// middleware(['throttle:6,1'])->
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'auth'])->name('auth');

Route::prefix('/')->middleware(['auth'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    Route::prefix('driver')->name('driver')->group(function () {
        Route::get('/', [DriverController::class, 'index'])->name('');
    });

    /**
     * SUPPLIER
     */
    Route::prefix('supplier')->name('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('');

        Route::prefix('claim')->name('.claim')->group(function () {
            Route::get('/add', [SupplierController::class, 'claim_add'])->name('.add');
            Route::post('/add', [SupplierController::class, 'claim_add_post'])->name('.add_post');
            Route::get('/edit/{id?}', [SupplierController::class, 'claim_edit'])->name('.edit');
            Route::post('/edit/{id?}', [SupplierController::class, 'claim_edit_post'])->name('.edit_post');
            Route::get('/slots', [SupplierController::class, 'findAvailableSlots'])->name('.slots');
        });

        Route::prefix('driver')->name('.driver')->group(function () {
            Route::get('/', [SupplierController::class, 'driver'])->name('');
            Route::get('/with_trashed', [SupplierController::class, 'driver'])->name('.with_trashed');
            Route::get('/one/{id?}', [SupplierController::class, 'driver_one'])->name('.one');
            Route::get('/add', [SupplierController::class, 'driver_add'])->name('.add');
            Route::post('/add', [SupplierController::class, 'driver_add_post'])->name('.add_post');
            Route::post('/edit/{id?}', [SupplierController::class, 'driver_edit_post'])->name('.edit_post');
            Route::post('/delete/{id?}', [SupplierController::class, 'driver_delete_post'])->name('.delete_post');
            Route::post('/restore/{id?}', [SupplierController::class, 'driver_restore_post'])->name('.restore_post');
            Route::get('/ac', [SupplierController::class, 'driver_ac'])->name('.ac');
        });

        Route::prefix('expeditor')->name('.expeditor')->group(function () {
            Route::get('/', [SupplierController::class, 'expeditor'])->name('');
            Route::get('/with_trashed', [SupplierController::class, 'expeditor'])->name('.with_trashed');
            Route::get('/one/{id?}', [SupplierController::class, 'expeditor_one'])->name('.one');
            Route::get('/add', [SupplierController::class, 'expeditor_add'])->name('.add');
            Route::post('/add', [SupplierController::class, 'expeditor_add_post'])->name('.add_post');
            Route::post('/edit/{id?}', [SupplierController::class, 'expeditor_edit_post'])->name('.edit_post');
            Route::post('/delete/{id?}', [SupplierController::class, 'expeditor_delete_post'])->name('.delete_post');
            Route::post('/restore/{id?}', [SupplierController::class, 'expeditor_restore_post'])->name('.restore_post');
            Route::get('/ac', [SupplierController::class, 'expeditor_ac'])->name('.ac');
        });


        Route::prefix('car')->name('.car')->group(function () {
            Route::get('/', [SupplierController::class, 'car'])->name('');
            Route::get('/add', [SupplierController::class, 'claim_add'])->name('.add');
            Route::post('/add', [SupplierController::class, 'claim_add_post'])->name('.add_post');
        });
    });
    /**
     * END SUPPLIER
     */


    /**
     * ADMIN
     */
    Route::prefix('admin')->name('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('');

        Route::prefix('user')->name('.user')->group(function () {
            crud_routes(AdminUsersController::class);
        });

        Route::prefix('settings')->name('.settings')->group(function () {
            crud_routes(AdminSettingsController::class);
        });

        Route::prefix('dictionary')->name('.dictionary')->group(function () {
            Route::get('/', [AdminController::class, 'dictionary'])->name('');

            Route::prefix('car_type')->name('.car_type')->group(function () {
                crud_routes(AdminDictionaryCarTypeController::class);
            });

            Route::prefix('gate')->name('.gate')->group(function () {
                crud_routes(AdminDictionaryGateController::class);
            });

            Route::prefix('acceptance')->name('.acceptance')->group(function () {
                crud_routes(AdminDictionaryAcceptanceController::class);
            });
        });
    });
    /**
     * END ADMIN
     */

});

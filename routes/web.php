<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDictionaryAcceptanceController;
use App\Http\Controllers\AdminDictionaryCarTypeController;
use App\Http\Controllers\AdminDictionaryGateController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\StockAdminController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDriverController;
use App\Http\Controllers\SupplierExpeditorController;
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

    /** MANAGER */
    Route::prefix('manager')->name('manager')->middleware('is_manager')->group(function () {
        Route::get('/', [ManagerController::class, 'index'])->name('');
    });
    /** END MANAGER */

    /** DRIVER */
    Route::prefix('driver')->name('driver')->middleware('is_driver')->group(function () {
        Route::get('/', [DriverController::class, 'index'])->name('');
    });
    /** END DRIVER */

    /** STOCK ADMIN */
    Route::prefix('stock_admin')->name('stock_admin')->middleware('is_stock_admin')->group(function () {
        Route::get('/', [StockAdminController::class, 'index'])->name('');
        Route::get('/supplier/ac', [StockAdminController::class, 'supplier_ac'])->name('.supplier.ac');
        Route::get('/rs_supplier/ac', [StockAdminController::class, 'rs_supplier_ac'])->name('.rs_supplier.ac');

        Route::prefix('supplier')->name('.supplier')->group(function () {
            Route::get('/', [StockAdminController::class, 'supplier'])->name('');
            Route::get('/with_trashed', [StockAdminController::class, 'supplier'])->name('.with_trashed');
            Route::get('/add', [StockAdminController::class, 'supplier_add'])->name('.add');
            Route::post('/add', [StockAdminController::class, 'supplier_add_post'])->name('.add_post');
            Route::get('/edit/{id?}', [StockAdminController::class, 'supplier_edit'])->name('.edit');
            Route::post('/edit/{id?}', [StockAdminController::class, 'supplier_edit_post'])->name('.edit_post');
        });

        Route::prefix('claim')->name('.claim')->group(function () {
            Route::get('/', [StockAdminController::class, 'claim'])->name('');
            Route::get('/with_trashed', [StockAdminController::class, 'claim'])->name('.with_trashed');
            Route::get('/add', [StockAdminController::class, 'claim_add'])->name('.add');
            Route::post('/add', [StockAdminController::class, 'claim_add_post'])->name('.add_post');
            Route::get('/edit/{id?}', [StockAdminController::class, 'claim_edit'])->name('.edit');
            Route::post('/edit/{id?}', [StockAdminController::class, 'claim_edit_post'])->name('.edit_post');
            Route::get('/slots', [StockAdminController::class, 'findAvailableSlots'])->name('.slots');
        });

        Route::prefix('driver')->name('.driver')->group(function () {
            Route::get('/', [StockAdminController::class, 'driver'])->name('');
            Route::get('/with_trashed', [StockAdminController::class, 'driver'])->name('.with_trashed');
            Route::get('/one/{id?}', [StockAdminController::class, 'driver_one'])->name('.one');
            Route::post('/add', [StockAdminController::class, 'driver_add_post'])->name('.add_post');
            Route::post('/edit/{id?}', [StockAdminController::class, 'driver_edit_post'])->name('.edit_post');
            Route::post('/delete/{id?}', [StockAdminController::class, 'driver_delete_post'])->name('.delete_post');
            Route::post('/restore/{id?}', [StockAdminController::class, 'driver_restore_post'])->name('.restore_post');
            Route::get('/ac', [StockAdminController::class, 'driver_ac'])->name('.ac');
        });

        Route::prefix('expeditor')->name('.expeditor')->group(function () {
            Route::get('/', [StockAdminController::class, 'expeditor'])->name('');
            Route::get('/with_trashed', [StockAdminController::class, 'expeditor'])->name('.with_trashed');
            Route::get('/one/{id?}', [StockAdminController::class, 'expeditor_one'])->name('.one');
            Route::get('/add', [StockAdminController::class, 'expeditor_add'])->name('.add');
            Route::post('/add', [StockAdminController::class, 'expeditor_add_post'])->name('.add_post');
            Route::post('/edit/{id?}', [StockAdminController::class, 'expeditor_edit_post'])->name('.edit_post');
            Route::post('/delete/{id?}', [StockAdminController::class, 'expeditor_delete_post'])->name('.delete_post');
            Route::post('/restore/{id?}', [StockAdminController::class, 'expeditor_restore_post'])->name('.restore_post');
            Route::get('/ac', [StockAdminController::class, 'expeditor_ac'])->name('.ac');
        });
    });
    /** END STOCK ADMIN */


    /** SUPPLIER */
    Route::prefix('supplier')->name('supplier')->middleware('is_supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('');
        Route::get('/profile', [SupplierController::class, 'profile'])->name('.profile');

        Route::prefix('claim')->name('.claim')->group(function () {
            Route::get('/', [SupplierController::class, 'claim'])->name('');
            Route::get('/with_trashed', [SupplierController::class, 'claim'])->name('.with_trashed');
            Route::get('/add', [SupplierController::class, 'claim_add'])->name('.add');
            Route::post('/add', [SupplierController::class, 'claim_add_post'])->name('.add_post');
            Route::get('/edit/{id?}', [SupplierController::class, 'claim_edit'])->name('.edit');
            Route::post('/edit/{id?}', [SupplierController::class, 'claim_edit_post'])->name('.edit_post');
            Route::get('/slots', [SupplierController::class, 'findAvailableSlots'])->name('.slots');
        });

        Route::prefix('driver')->name('.driver')->group(function () {
            Route::get('/', [SupplierDriverController::class, 'driver'])->name('');
            Route::get('/with_trashed', [SupplierDriverController::class, 'driver'])->name('.with_trashed');
            Route::get('/one/{id?}', [SupplierDriverController::class, 'driver_one'])->name('.one');
            Route::get('/add', [SupplierDriverController::class, 'driver_add'])->name('.add');
            Route::post('/add', [SupplierDriverController::class, 'driver_add_post'])->name('.add_post');
            Route::post('/edit/{id?}', [SupplierDriverController::class, 'driver_edit_post'])->name('.edit_post');
            Route::post('/delete/{id?}', [SupplierDriverController::class, 'driver_delete_post'])->name('.delete_post');
            Route::post('/restore/{id?}', [SupplierDriverController::class, 'driver_restore_post'])->name('.restore_post');
            Route::get('/ac', [SupplierDriverController::class, 'driver_ac'])->name('.ac');
        });

        Route::prefix('expeditor')->name('.expeditor')->group(function () {
            Route::get('/', [SupplierExpeditorController::class, 'expeditor'])->name('');
            Route::get('/with_trashed', [SupplierExpeditorController::class, 'expeditor'])->name('.with_trashed');
            Route::get('/one/{id?}', [SupplierExpeditorController::class, 'expeditor_one'])->name('.one');
            Route::get('/add', [SupplierExpeditorController::class, 'expeditor_add'])->name('.add');
            Route::post('/add', [SupplierExpeditorController::class, 'expeditor_add_post'])->name('.add_post');
            Route::post('/edit/{id?}', [SupplierExpeditorController::class, 'expeditor_edit_post'])->name('.edit_post');
            Route::post('/delete/{id?}', [SupplierExpeditorController::class, 'expeditor_delete_post'])->name('.delete_post');
            Route::post('/restore/{id?}', [SupplierExpeditorController::class, 'expeditor_restore_post'])->name('.restore_post');
            Route::get('/ac', [SupplierExpeditorController::class, 'expeditor_ac'])->name('.ac');
        });


        Route::prefix('car')->name('.car')->group(function () {
            Route::get('/', [SupplierController::class, 'car'])->name('');
            Route::get('/add', [SupplierController::class, 'claim_add'])->name('.add');
            Route::post('/add', [SupplierController::class, 'claim_add_post'])->name('.add_post');
        });
    });
    /** END SUPPLIER */


    /** ADMIN */
    Route::prefix('admin')->name('admin')->middleware('is_admin')->group(function () {
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
    /** END ADMIN */

});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->group(function () {
        Route::get('/units/getUnits', [UnitController::class, 'getUnits'])->name('units.data');
        Route::resource('/units', UnitController::class);
        
        Route::get('/items/getItems', [ItemController::class, 'getItems'])->name('items.data');
        Route::resource('/items', ItemController::class);

        Route::get('/positions/getPositions', [PositionController::class, 'getPositions'])->name('positions.data');
        Route::resource('/positions', PositionController::class);

        Route::get('/employees/getEmployees', [EmployeeController::class,  'getEmployees'])->name('employees.data');
        Route::resource('/employees', EmployeeController::class);

        Route::get('/brands/getBrands', [BrandController::class, 'getBrands'])->name('brands.data');
        Route::resource('/brands', BrandController::class);

        Route::get('/assets/getAssets', [AssetController::class, 'getAssets'])->name('assets.data');
        Route::resource('/assets', AssetController::class);
    });
});

require __DIR__ . '/auth.php';

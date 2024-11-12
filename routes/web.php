<?php

use App\Models\Inbound;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoanTempController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\InboundTempController;
use App\Http\Controllers\OutboundTempController;

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

        Route::post('/employees/store/headOffice', [EmployeeController::class,  'storeHeadOffice'])->name('employees.store.headOffice');
        Route::get('/employees/getEmployees', [EmployeeController::class,  'getEmployees'])->name('employees.data');
        Route::resource('/employees', EmployeeController::class);

        Route::get('/brands/getBrands', [BrandController::class, 'getBrands'])->name('brands.data');
        Route::resource('/brands', BrandController::class);

        Route::get('/assets/getAssets', [AssetController::class, 'getAssets'])->name('assets.data');
        Route::resource('/assets', AssetController::class);
    });

    Route::prefix('super-admin')->group(function () {
        Route::get('/branches/getbranches', [BranchController::class, 'getbranches'])->name('branches.data');
        Route::resource('/branches', BranchController::class);

        Route::post('/get-regencies', [BranchController::class, 'getRegencies']);
        Route::post('/get-districts', [BranchController::class, 'getDistricts']);
        Route::post('/get-villages', [BranchController::class, 'getVillages']);
    });

    Route::get('/inbounds/generate-code', function () {
        $count = DB::table('inbounds')->count() + 1;
        $branchCode = Auth::user()->branchOffice->code;

        $code = sprintf("%d/" . $branchCode . "/BPG/%d", $count, now()->year);

        return response()->json(['code' => $code]);
    });

    Route::get('/outbounds/generate-code', function () {
        $count = DB::table('outbounds')->count() + 1;
        $branchCode = Auth::user()->branchOffice->code;

        $code = sprintf("%d/" . $branchCode . "/BPB/%d", $count, now()->year);

        return response()->json(['code' => $code]);
    });

    Route::get('/loans/generate-code', function () {
        $count = DB::table('loans')->count() + 1;
        $branchCode = Auth::user()->branchOffice->code;

        $code = sprintf("%d/" . $branchCode . "/BPPPA/%d", $count, now()->year);

        return response()->json(['code' => $code]);
    });

    Route::get('/inbounds/check', [InboundTempController::class, 'checkInbound'])->name('inbounds.check');
    Route::post('/inbounds/cancel', [InboundTempController::class, 'cancelInbound'])->name('inbounds.cancel');
    Route::post('/inbounds/confirm', [InboundTempController::class, 'storeInbound'])->name('inbounds.confirm');
    Route::get('/inbounds/getInbounds', [InboundTempController::class, 'getInbounds'])->name('inbounds.temp.data');
    Route::resource('/inbounds', InboundTempController::class);

    Route::get('/outbounds/check', [OutboundTempController::class, 'checkOutbound'])->name('outbounds.check');
    Route::post('/outbounds/cancel', [OutboundTempController::class, 'cancelOutbound'])->name('outbounds.cancel');
    Route::post('/outbounds/confirm', [OutboundTempController::class, 'storeOutbound'])->name('outbounds.confirm');
    Route::get('/outbounds/getOutbounds', [OutboundTempController::class, 'getOutbounds'])->name('outbounds.temp.data');
    Route::resource('/outbounds', OutboundTempController::class);

    Route::get('/loans/check', [LoanTempController::class, 'checkLoan'])->name('loans.check');
    Route::post('/loans/cancel', [LoanTempController::class, 'cancelLoan'])->name('loans.cancel');
    Route::post('/loans/confirm', [LoanTempController::class, 'storeLoan'])->name('loans.confirm');
    Route::get('/loans/getLoans', [LoanTempController::class, 'getLoans'])->name('loans.temp.data');
    Route::resource('/loans', LoanTempController::class);


    Route::get('/fetch-date', function () {
        return response()->json([
            'date' => now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm:ss')
        ]);
    })->name('fetch.date');
});

Route::get('/inbounds/receipt/{id}', [InboundTempController::class, 'receipt'])
    ->name('inbounds.receipt');

Route::get('/outbounds/receipt/{id}', [OutboundTempController::class, 'receipt'])
    ->name('outbounds.receipt');

Route::get('/loans/receipt/{id}', [LoanTempController::class, 'receipt'])
    ->name('loans.receipt');

Route::get('/inbounds/get', [InboundTempController::class, 'getInbounds']);
require __DIR__ . '/auth.php';

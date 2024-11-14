<?php

use App\Models\Inbound;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoanTempController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\InboundTempController;
use App\Http\Controllers\ReturnAssetController;
use App\Http\Controllers\OutboundTempController;
use App\Http\Controllers\DocumentSigningController;

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
        Route::get('/units/getUnits', [UnitController::class, 'getUnits'])->name('units.data')->middleware('role_or_permission:admin|manage-items');
        Route::resource('/units', UnitController::class)->middleware('role_or_permission:admin|manage-items');

        Route::get('/items/getItems', [ItemController::class, 'getItems'])->name('items.data')->middleware('role_or_permission:admin|manage-items');
        Route::resource('/items', ItemController::class)->middleware('role_or_permission:admin|manage-items');

        Route::get('/positions/getPositions', [PositionController::class, 'getPositions'])->name('positions.data')->middleware('role_or_permission:admin|manage-positions');
        Route::resource('/positions', PositionController::class)->middleware('role_or_permission:admin|manage-positions');

        Route::post('/employees/store/headOffice', [EmployeeController::class,  'storeHeadOffice'])->name('employees.store.headOffice')->middleware('role_or_permission:admin|manage-employees');
        Route::get('/employees/getEmployees', [EmployeeController::class,  'getEmployees'])->name('employees.data')->middleware('role_or_permission:admin|manage-employees');
        Route::resource('/employees', EmployeeController::class)->middleware('role_or_permission:admin|manage-employees');

        Route::get('/brands/getBrands', [BrandController::class, 'getBrands'])->name('brands.data')->middleware('role_or_permission:admin|manage-brands');
        Route::resource('/brands', BrandController::class)->middleware('role_or_permission:admin|manage-brands');

        Route::get('/assets/getAssets', [AssetController::class, 'getAssets'])->name('assets.data')->middleware('role_or_permission:admin|manage-assets');
        Route::resource('/assets', AssetController::class)->middleware('role_or_permission:admin|manage-assets');

        Route::get('/users/getUsers', [UserController::class, 'getUsers'])->name('users.data')->middleware('role_or_permission:admin|manage-users');
        Route::resource('/users', UserController::class)->middleware('role_or_permission:admin|manage-users');

        Route::get('/loans/getLoan', [LoanController::class, 'getLoan'])->name('loans.data');
        Route::get('/loans/getLoanAsset', [LoanController::class, 'getLoanAssets'])->name('loans.assets.data');
        Route::resource('/loans', LoanController::class);
    });

    Route::prefix('super-admin')->group(function () {
        Route::get('/branches/getbranches', [BranchController::class, 'getbranches'])->name('branches.data');
        Route::resource('/branches', BranchController::class);

        Route::get('/admins/getadmins', [AdminController::class, 'getadmins'])->name('admins.data');
        Route::resource('/admins', AdminController::class);

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

    Route::post('/loans/search', [LoanTempController::class, 'search'])->name('loans.search');
    Route::get('/loans/check', [LoanTempController::class, 'checkLoan'])->name('loans.check');
    Route::post('/loans/cancel', [LoanTempController::class, 'cancelLoan'])->name('loans.cancel');
    Route::post('/loans/confirm', [LoanTempController::class, 'storeLoan'])->name('loans.confirm');
    Route::get('/loans/getLoans', [LoanTempController::class, 'getLoans'])->name('loans.temp.data');
    Route::resource('/loans', LoanTempController::class);

    Route::get('/returns/check', [ReturnAssetController::class, 'checkReturn'])->name('returns.check');
    Route::post('/returns/cancel', [ReturnAssetController::class, 'cancelReturn'])->name('returns.cancel');
    Route::post('/returns/confirm', [ReturnAssetController::class, 'storeReturn'])->name('returns.confirm');
    Route::get('/returns/getReturns', [ReturnAssetController::class, 'getReturns'])->name('returns.data');
    Route::resource('/returns', ReturnAssetController::class);

    Route::get('/permits/getPermits', [PermitController::class, 'getPermits'])->name('permits.data');
    Route::resource('/permits', PermitController::class);


    Route::get('/fetch-date', function () {
        return response()->json([
            'date' => now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm:ss')
        ]);
    })->name('fetch.date');


    // DOCUMENT SIGNING
    Route::post('/documents/sign/{id}/store', [DocumentSigningController::class, 'storeSignature'])->name('documents.sign.store');

    Route::get('/documents/loans', [DocumentSigningController::class, 'loanIndex'])->name('documents.loans.index');
    Route::get('/documents/getPendingLoans', [DocumentSigningController::class, 'getPendingLoans'])->name('documents.loans.pending');
    Route::get('/documents/loans/sign/{id}', [DocumentSigningController::class, 'signLoan'])->name('documents.loans.sign');
    Route::get('/documents/loans/preview/{id}', [DocumentSigningController::class, 'LoanPreview'])->name('documents.loans.preview');

    Route::get('/documents/outbounds', [DocumentSigningController::class, 'outboundIndex'])->name('documents.outbounds.index');

    Route::get('/documents/{document}/sign', [DocumentSigningController::class, 'signIndex'])
        ->name('documents.sign');

    Route::get('/documents/{document}/preview', [DocumentSigningController::class, 'preview'])
        ->name('document.preview');

    Route::post('/signatures', [DocumentSigningController::class, 'storeSignature'])
        ->name('signature.store');
});

Route::get('/inbounds/receipt/{id}', [InboundTempController::class, 'receipt'])
    ->name('inbounds.receipt');

Route::get('/outbounds/receipt/{id}', [OutboundTempController::class, 'receipt'])
    ->name('outbounds.receipt');

Route::get('/loans/receipt/{id}', [LoanTempController::class, 'receipt'])
    ->name('loans.receipt');



Route::get('/inbounds/get', [InboundTempController::class, 'getInbounds']);
require __DIR__ . '/auth.php';

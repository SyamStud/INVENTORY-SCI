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
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoanTempController;
use App\Http\Controllers\OutboundController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\InboundTempController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ReturnAssetController;
use App\Http\Controllers\OutboundTempController;
use App\Http\Controllers\DocumentSigningController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ADMIN ROUTES
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/getLoanAssets', [DashboardController::class, 'getLoanAssets'])->name('dashboard.getLoanAssets');

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
        Route::get('/assets/other/getAssets', [AssetController::class, 'getAssetsOtherBranch'])->name('assets.other.data')->middleware('role_or_permission:admin|manage-assets');
        Route::get('/assets/other', [AssetController::class, 'otherBranch'])->name('assets.other')->middleware('role_or_permission:admin|manage-assets');
        Route::resource('/assets', AssetController::class)->middleware('role_or_permission:admin|manage-assets');

        Route::get('/users/getUsers', [UserController::class, 'getUsers'])->name('users.data')->middleware('role_or_permission:admin|manage-users');
        Route::resource('/users', UserController::class)->middleware('role_or_permission:admin|manage-users');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('role_or_permission:admin|manage-users');
        Route::put('/permissions', [PermissionController::class, 'update'])->name('permissions.update')->middleware('role_or_permission:admin|manage-users');
        Route::get('/get-user-permissions/{user}', [PermissionController::class, 'getUserPermissions'])
            ->name('permissions.get-user')->middleware('role_or_permission:admin|manage-users');

        // HISTORY
        Route::middleware('role:admin|employee')->group(function () {
            Route::get('/inbounds/getInbound', [InboundController::class, 'getInbound'])->name('inbounds.data');
            Route::get('/inbounds/getInboundItem', [InboundController::class, 'getInboundItems'])->name('inbounds.items.data');
            Route::name('admin.')->group(function () {
                Route::resource('/inbounds', InboundController::class);
            });

            Route::get('/outbounds/getOutbound', [OutboundController::class, 'getOutbound'])->name('outbounds.data');
            Route::get('/outbounds/getOutboundItem', [OutboundController::class, 'getOutboundItems'])->name('outbounds.items.data');
            Route::name('admin.')->group(function () {
                Route::resource('/outbounds', OutboundController::class);
            });

            Route::get('/loans/getLoan', [LoanController::class, 'getLoan'])->name('loans.data');
            Route::get('/loans/getLoanAsset', [LoanController::class, 'getLoanAssets'])->name('loans.assets.data');
            Route::name('admin.')->group(function () {
                Route::resource('/loans', LoanController::class);
            });

            Route::get('/returns/getReturn', [ReturnController::class, 'getReturn'])->name('admin.returns.data');
            Route::get('/returns/getReturnAsset', [ReturnController::class, 'getReturnAssets'])->name('returns.assets.data');
            Route::name('admin.')->group(function () {
                Route::resource('/returns', ReturnController::class);
            });
        });
    });


    // SUPER ADMIN ROUTES
    Route::prefix('super-admin')->group(function () {
        Route::middleware('role:super-admin')->group(function () {
            Route::get('/branches/getbranches', [BranchController::class, 'getbranches'])->name('branches.data');
            Route::resource('/branches', BranchController::class);

            Route::get('/admins/getadmins', [AdminController::class, 'getadmins'])->name('admins.data');
            Route::resource('/admins', AdminController::class);

            Route::post('/get-regencies', [BranchController::class, 'getRegencies']);
            Route::post('/get-districts', [BranchController::class, 'getDistricts']);
            Route::post('/get-villages', [BranchController::class, 'getVillages']);
        });
    });


    // DOCUMENTS SIGNING
    Route::prefix('documents')->group(function () {
        Route::middleware('role_or_permission:admin|sign-documents')->group(function () {
            Route::post('/sign/{id}/store', [DocumentSigningController::class, 'storeSignature'])->name('documents.sign.store');

            Route::get('/loans', [DocumentSigningController::class, 'loanIndex'])->name('documents.loans.index');
            Route::get('/getPendingLoans', [DocumentSigningController::class, 'getPendingLoans'])->name('documents.loans.pending');
            Route::get('/loans/sign/{id}', [DocumentSigningController::class, 'signLoan'])->name('documents.loans.sign');
            Route::get('/loans/preview/{id}', [DocumentSigningController::class, 'LoanPreview'])->name('documents.loans.preview');
            Route::get('/loans/download/{id}/{preview}', [LoanTempController::class, 'saveDocument'])->name('documents.loans.download');

            Route::get('/outbounds', [DocumentSigningController::class, 'outboundIndex'])->name('documents.outbounds.index');
            Route::get('/getPendingOutbounds', [DocumentSigningController::class, 'getPendingOutbounds'])->name('documents.outbounds.pending');
            Route::get('/outbounds/sign/{id}', [DocumentSigningController::class, 'signOutbound'])->name('documents.outbounds.sign');
            Route::get('/outbounds/preview/{id}', [DocumentSigningController::class, 'OutboundPreview'])->name('documents.outbounds.preview');
            Route::get('/outbounds/download/{id}/{preview}', [OutboundTempController::class, 'saveDocument'])->name('documents.outbounds.download');

            Route::get('/returns/download/{id}/{preview}/{number}', [ReturnAssetController::class, 'saveDocument'])->name('documents.returns.download');
        });
    });

    // MAIN ROUTES
    Route::middleware('role_or_permission:admin|manage-inventories')->group(function () {
        // GENERATE CODE
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
        Route::get('/outbounds/sign/{id}', [OutboundTempController::class, 'signOutbound'])->name('outbounds.sign');
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

        Route::get('/procurements/getProcurements', [ProcurementController::class, 'getProcurements'])->name('procurements.data');
        Route::resource('/procurements', ProcurementController::class);

        Route::get('/loans/receipt/{id}', [LoanTempController::class, 'receipt'])
            ->name('loans.receipt');
    });
});

require __DIR__ . '/auth.php';

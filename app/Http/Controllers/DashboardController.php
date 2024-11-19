<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\LoanAsset;
use App\Models\BranchOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        $branches = BranchOffice::all();

        return view('pages.admin.dashboard', [
            'branches' => $branches
        ]);
    }

    public function getLoanAssets()
    {
        Carbon::setLocale('id');

        $loanAssets = LoanAsset::where('branch_id', Auth::user()->branch_id)
            ->with('loan', 'asset', 'asset.brand')
            ->get();

        return DataTables::of($loanAssets)
            ->addIndexColumn()
            ->addColumn('loan_number', function ($loanAsset) {
                return $loanAsset->loan->loan_number;
            })
            ->addColumn('customer_name', function ($loanAsset) {
                return $loanAsset->loan->customer_name;
            })
            ->addColumn('tag_number', function ($loanAsset) {
                return $loanAsset->asset->tag_number;
            })
            ->addColumn('asset', function ($loanAsset) {
                return $loanAsset->asset->name;
            })
            ->addColumn('brand', function ($loanAsset) {
                return $loanAsset->asset->brand->name;
            })
            ->addColumn('duration', function ($loanAsset) {
                return $loanAsset->duration . ' hari';
            })
            ->addColumn('return_estimation', function ($loanAsset) {
                return $loanAsset->created_at->addDays($loanAsset->duration)->translatedFormat('d F Y');
            })
            ->addColumn('created_at', function ($loanAsset) {
                return $loanAsset->created_at->translatedFormat('d F Y');
            })
            ->make(true);
    }
}

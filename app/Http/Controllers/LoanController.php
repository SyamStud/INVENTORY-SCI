<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Loan::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.admin.loans', [
            'brands' => $brands
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Controller Method
    public function getLoan()
    {
        $loans = Loan::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');
        Debugbar::info($loans);

        return DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('operation_head', function ($loans) {
                return $loans->user->name;
            })
            ->addColumn('loan_officer', function ($loans) {
                return $loans->user->name;
            })
            ->addColumn('general_division', function ($loans) {
                return $loans->generalDivision->name;
            })
            ->addColumn('detail', function ($loans) {
                $loansJson = htmlspecialchars(json_encode($loans), ENT_QUOTES, 'UTF-8');

                return "
                    <div class='flex items-center gap-2'>
                        <button style='background-color: #133E87;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                            x-data='' 
                            x-on:click.prevent=\"
                                \$dispatch('open-modal', 'detail-loan');
                                \$dispatch('set-loans', {$loansJson})
                            \">
                            <img class='w-5' src='https://img.icons8.com/?size=100&id=VNxIqSP5pHwD&format=png&color=FFFFFF' alt=''>
                            Detail
                        </button>
                    </div>
                ";
            })
            ->rawColumns(['detail'])
            ->make(true);
    }

    public function getLoanAssets()
    {
        $loans = LoanAsset::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        $dataTable = DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('nama_asset', function ($loan) {
                
                return $loan->asset->name;
            })
            ->make(true);

        return $dataTable;
    }

}

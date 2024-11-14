<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }

    public function getPendingLoans()
    {
        $loans = Loan::where('status', 'pending')
            ->where('branch_id', Auth::user()->branch_id)
            ->get();

        return DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('action', function ($loan) {
                return "
                <div class=''>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='/document/loan/sign/" . $loan->id . "'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                        Tanda Tangan
                    </a>
                </div>";
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}

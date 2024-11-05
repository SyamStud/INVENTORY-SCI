<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.admin.units');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $isExist = Unit::where('name', $request->name)->where('branch_id', Auth::user()->branch_id)->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Satuan sudah terdaftar',
            ]);
        }

        Unit::create([
            'name' => $request->name,
            'branch_id' => 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $unit->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil dihapus',
        ]);
    }

    // Controller Method
    public function getUnits()
    {
        $units = Unit::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($units)
            ->addIndexColumn()
            ->addColumn('action', function ($unit) {
                $unitJson = htmlspecialchars(json_encode($unit), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-unit');
                            \$dispatch('set-unit', {$unitJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-unit');
                            \$dispatch('set-unit', {$unitJson})
                        \">
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=7DbfyX80LGwU&format=png&color=FFFFFF' alt=''>
                        Hapus
                    </button>
                </div>
            ";
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}

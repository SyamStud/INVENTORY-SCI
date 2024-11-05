<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\Debugbar\Twig\Extension\Debug;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.admin.positions');
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

        $isExist = Position::where('name', $request->name)->where('branch_id', Auth::user()->branch_id)->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Posisi sudah terdaftar',
            ]);
        }

        Position::create([
            'name' => $request->name,
            'branch_id' => Auth::user()->branch_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $isExist = Position::where('name', $request->name)->where('branch_id', Auth::user()->branch_id)->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Posisi sudah terdaftar',
            ]);
        }

        $position->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        $position->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil dihapus',
        ]);
    }

    /**
     * Get data for datatables.
     */

    public function getPositions()
    {
        $positions = Position::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($positions)
            ->addIndexColumn()
            ->addColumn('action', function ($position) {
                $positionJson = htmlspecialchars(json_encode($position), ENT_QUOTES, 'UTF-8');

                Debugbar::info($positionJson);

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-position');
                            \$dispatch('set-position', {$positionJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-position');
                            \$dispatch('set-position', {$positionJson})
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

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.admin.vehicles');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nopol' => 'required',
            'brand' => 'required',
            'stnk' => 'required',
            'kir' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $isExist = Vehicle::where('nopol', $request->nopol)->where('branch_id', Auth::user()->branch_id)->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor polisi sudah terdaftar',
            ]);
        }

        Vehicle::create([
            'nopol' => $request->nopol,
            'brand' => $request->brand,
            'stnk' => $request->stnk,
            'kir' => $request->kir,
            'branch_id' => Auth::user()->branch_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kendaraan berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validation = Validator::make($request->all(), [
            'nopol' => 'required',
            'brand' => 'required',
            'stnk' => 'required',
            'kir' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $vehicle->update([
            'nopol' => $request->nopol,
            'brand' => $request->brand,
            'stnk' => $request->stnk,
            'kir' => $request->kir,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kendaraan berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kendaraan berhasil dihapus',
        ]);
    }

    // Controller Method
    public function getVehicles()
    {
        Carbon::setLocale('id');

        $vehicles = Vehicle::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');
        return DataTables::of($vehicles)
            ->addIndexColumn()
            ->addColumn('stnk', function ($vehicle) {
                return Carbon::parse($vehicle->stnk)->translatedFormat('d F Y');
            })
            ->addColumn('kir', function ($vehicle) {
                return Carbon::parse($vehicle->kir)->translatedFormat('d F Y');
            })
            ->addColumn('action', function ($vehicle) {
                $vehicleJson = htmlspecialchars(json_encode($vehicle), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-vehicle');
                            \$dispatch('set-vehicle', {$vehicleJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-vehicle');
                            \$dispatch('set-vehicle', {$vehicleJson})
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

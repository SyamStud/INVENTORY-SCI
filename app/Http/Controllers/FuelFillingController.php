<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vehicle;
use App\Models\FuelFilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class FuelFillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.admin.fuel-fillings', [
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'vehicle_id' => 'required',
            'km_fillings' => 'required',
            'quantity' => 'required',
            'driver' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        FuelFilling::create([
            'vehicle_id' => $request->vehicle_id,
            'km_fillings' => $request->km_fillings,
            'quantity' => $request->quantity,
            'driver' => $request->driver,
            'branch_id' => Auth::user()->branch_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Penggunaan berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $fuelFilling = FuelFilling::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'vehicle_id' => 'required',
            'km_fillings' => 'required',
            'quantity' => 'required',
            'driver' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $fuelFilling->update([
            'vehicle_id' => $request->vehicle_id,
            'km_fillings' => $request->km_fillings,
            'quantity' => $request->quantity,
            'driver' => $request->driver,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengisian berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fuelFilling = FuelFilling::findOrFail($id);
        
        $fuelFilling->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Penggunaan berhasil dihapus',
        ]);
    }

    // Controller Method
    public function getFuelFillings()
    {
        Carbon::setLocale('id');

        $fuelFillings = FuelFilling::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');
        return DataTables::of($fuelFillings)
            ->addIndexColumn()
            ->addColumn('nopol', function ($fuelFilling) {
                return $fuelFilling->vehicle->nopol;
            })
            ->addColumn('brand', function ($fuelFilling) {
                return $fuelFilling->vehicle->brand;
            })
            ->addColumn('quantity', function ($fuelFilling) {
                return 'Rp ' . number_format($fuelFilling->quantity, 0, ',', '.');
            })
            ->addColumn('action', function ($fuelFilling) {
                $fuelFillingJson = htmlspecialchars(json_encode($fuelFilling), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-filling');
                            \$dispatch('set-filling', {$fuelFillingJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-filling');
                            \$dispatch('set-filling', {$fuelFillingJson})
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

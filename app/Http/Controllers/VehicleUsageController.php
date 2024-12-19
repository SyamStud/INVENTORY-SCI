<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\VehicleUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class VehicleUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::where('branch_id', Auth::user()->branch_id)->get();
        $employees = Employee::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.main.vehicleUsages', [
            'vehicles' => $vehicles,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'vehicle_id' => 'required',
            'date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'purpose' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        VehicleUsage::create([
            'vehicle_id' => $request->vehicle_id,
            'date' => $request->date,
            'employee_id' => $request->employee_id,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
            'purpose' => $request->purpose,
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
    public function update(Request $request, VehicleUsage $vehicleUsage)
    {
        $validation = Validator::make($request->all(), [
            'vehicle_id' => 'required',
            'date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'time_start' => 'required',
            'time_end' => 'required',
            'purpose' => 'required',
            'driver' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $vehicleUsage->update([
            'vehicle_id' => $request->vehicle_id,
            'date' => $request->date,
            'employee_id' => $request->employee_id,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
            'purpose' => $request->purpose,
            'driver' => $request->driver,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengunaan berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleUsage $vehicleUsage)
    {
        $vehicleUsage->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Penggunaan berhasil dihapus',
        ]);
    }

    // Controller Method
    public function getVehicleUsages()
    {
        Carbon::setLocale('id');

        $vehicleUsages = VehicleUsage::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');
        return DataTables::of($vehicleUsages)
            ->addIndexColumn()
            ->addColumn('date', function ($vehicleUsage) {
                return Carbon::parse($vehicleUsage->date)->translatedFormat('d F Y');
            })
            ->addColumn('time_start', function ($vehicleUsage) {
                return Carbon::parse($vehicleUsage->time_start)->format('H:i');
            })
            ->addColumn('time_end', function ($vehicleUsage) {
                return Carbon::parse($vehicleUsage->time_end)->format('H:i');
            })
            ->addColumn('employee', function ($vehicleUsage) {
                return $vehicleUsage->employee->name;
            })
            ->addColumn('action', function ($vehicleUsage) {
                $vehicleUsageJson = htmlspecialchars(json_encode($vehicleUsage), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-usage');
                            \$dispatch('set-usage', {$vehicleUsageJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-usage');
                            \$dispatch('set-usage', {$vehicleUsageJson})
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

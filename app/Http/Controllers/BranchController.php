<?php

namespace App\Http\Controllers;

use App\Models\Regency;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use App\Models\BranchOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $provinces = Province::all();
        return view('pages.superAdmin.branches', [
            'provinces' => $provinces,
        ]);
    }

    public function getRegencies(Request $request)
    {
        $regencies = Regency::where('province_id', $request->province_id)->get();
        return response()->json($regencies);
    }

    public function getDistricts(Request $request)
    {
        $districts = District::where('regency_id', $request->regency_id)->get();
        return response()->json($districts);
    }

    public function getVillages(Request $request)
    {
        $villages = Village::where('district_id', $request->district_id)->get();
        return response()->json($villages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required',
            'province_id' => 'required',
            'regency_id' => 'required',
            'district_id' => 'required',
            'village_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        BranchOffice::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kantor cabang berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BranchOffice $branchOffice)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required',
            'province_id' => 'required',
            'regency_id' => 'required',
            'district_id' => 'required',
            'village_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $branchOffice->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kantor cabang berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $branchOffice = BranchOffice::find($id);
        $branchOffice->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kantor cabang berhasil dihapus',
        ]);
    }

    /**
     * Get all branches
     */
    public function getBranches()
    {
        $branches = BranchOffice::orderByDesc('created_at')->get();

        return DataTables::of($branches)
            ->addIndexColumn()
            ->addColumn('name', function ($branch) {
                return ucwords(strtolower($branch->name));
            })
            ->addColumn('province', function ($branch) {
                return ucwords(strtolower($branch->province->name));
            })
            ->addColumn('regency', function ($branch) {
                return ucwords(strtolower($branch->regency->name));
            })
            ->addColumn('district', function ($branch) {
                return ucwords(strtolower($branch->district->name));
            })
            ->addColumn('village', function ($branch) {
                return ucwords(strtolower($branch->village->name));
            })
            ->addColumn('action', function ($branch) {
                $branchJson = htmlspecialchars(json_encode($branch), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-branch');
                            \$dispatch('set-branch', {$branchJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-branch');
                            \$dispatch('set-branch', {$branchJson})
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

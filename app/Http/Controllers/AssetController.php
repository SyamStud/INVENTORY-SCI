<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\BranchOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.admin.assets', [
            'brands' => $brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'inventory_number' => 'required',
            'tag_number' => 'required',
            'name' => 'required',
            'brand_id' => 'required',
            'serial_number' => 'required',
            'color' => 'required',
            'size' => 'required',
            'condition' => 'required|in:baik,rusak',
            'status' => 'required|in:terpakai,tidak terpakai',
            'calibration_number' => 'required',
            'calibration_interval' => 'required|integer',
            'calibration_start_date' => 'required|date',
            'calibration_due_date' => 'required|date',
            'calibration_institution' => 'required',
            'calibration_type' => 'required',
            'range' => 'required',
            'correction_factor' => 'required',
            'significance' => 'required|in:ya,tidak',
            'calibration.*' => 'required|mimes:pdf',
            'procurement' => 'required',
            'photo' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $asset = new Asset();
        $asset->inventory_number = $request->inventory_number;
        $asset->tag_number = $request->tag_number;
        $asset->name = $request->name;
        $asset->brand_id = $request->brand_id;
        $asset->serial_number = $request->serial_number;
        $asset->color = $request->color;
        $asset->size = $request->size;
        $asset->condition = $request->condition;
        $asset->status = $request->status;
        $asset->calibration_number = $request->calibration_number;
        $asset->calibration_interval = $request->calibration_interval;
        $asset->calibration_start_date = $request->calibration_start_date;
        $asset->calibration_due_date = $request->calibration_due_date;
        $asset->calibration_institution = $request->calibration_institution;
        $asset->calibration_type = $request->calibration_type;
        $asset->range = $request->range;
        $asset->correction_factor = $request->correction_factor;
        $asset->significance = $request->significance;

        $photo = $request->file('photo');
        $photoName = time() . '.' . $photo->extension();

        $photoPath = $photo->storeAs('assets/photos', $photoName, 'public');
        $asset->photo = 'assets/photos/' . $photoName;

        $calibrationFiles = [];

        $procurement = $request->file('procurement');
        $procurementName = time() . '.' . $procurement->extension();

        $procurementPath = $procurement->storeAs('assets/procurements', $procurementName, 'public');
        $asset->procurement = 'assets/procurements/' . $procurementName;

        $calibrationFiles = [];

        if ($request->hasFile('calibration')) {
            foreach ($request->file('calibration') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('assets/calibrations', $fileName, 'public');

                $calibrationFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ];
            }
        }

        // $file = $request->file('calibration');
        // $fileName = time() . '.' . $file->extension();

        // $filePath = $file->storeAs('assets', $fileName, 'public');
        $asset->calibration = json_encode($calibrationFiles);

        $asset->branch_id = Auth::user()->branch_id;
        $asset->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validation = Validator::make($request->all(), [
            'inventory_number' => 'required',
            'name' => 'required',
            'serial_number' => 'required',
            'brand_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $asset->inventory_number = $request->inventory_number;
        $asset->name = $request->name;
        $asset->serial_number = $request->serial_number;
        $asset->brand_id = $request->brand_id;

        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($asset->photo);

            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->extension();

            $photoPath = $photo->storeAs('assets/photos', $photoName, 'public');
            $asset->photo = 'assets/photos' . $photoName;
        }

        if ($request->hasFile('calibration')) {
            Storage::disk('public')->delete($asset->calibration);

            $file = $request->file('calibration');
            $fileName = time() . '.' . $file->extension();

            $filePath = $file->storeAs('assets/calibrations', $fileName, 'public');
            $asset->calibration = 'assets/calibrations' . $fileName;
        }

        if ($request->hasFile('procurement')) {
            Storage::disk('public')->delete($asset->procurement);

            $file = $request->file('procurement');
            $fileName = time() . '.' . $file->extension();

            $filePath = $file->storeAs('assets/procurements', $fileName, 'public');
            $asset->procurement = 'assets/procurements' . $fileName;
        }

        $asset->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diubah'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        Storage::disk('public')->delete($asset->photo);
        Storage::disk('public')->delete($asset->calibration);
        Storage::disk('public')->delete($asset->procurement);

        $asset->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function getAssets()
    {
        $assets = Asset::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($assets)
            ->addIndexColumn()
            ->addColumn('brand', function ($asset) {
                return $asset->brand->name;
            })
            ->addColumn('calibration', function ($asset) {
                $calibrationFiles = json_decode($asset->calibration, true);
                $buttons = "<div class='flex gap-2'>";

                foreach ($calibrationFiles as $file) {
                    $buttons .= "
                    <div class='flex items-center gap-2'>
                        <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $file['path']) . "' target='_blank'>
                            <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                            " . $file['name'] . "
                        </a>
                    </div>";
                }

                $buttons .= "</div>";
                return $buttons;
            })
            ->addColumn('procurement', function ($asset) {
                return "
                <div class=''>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $asset->procurement) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                        Lihat
                    </a>
                </div>";
            })
            ->addColumn('photo', function ($asset) {
                return "
                <div class=''>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $asset->photo) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=VNxIqSP5pHwD&format=png&color=FFFFFF' alt=''>
                        Lihat
                    </a>
                </div>";
            })
            ->addColumn('action', function ($asset) {
                $assetJson = htmlspecialchars(json_encode($asset), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-asset');
                            \$dispatch('set-asset', {$assetJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-asset');
                            \$dispatch('set-asset', {$assetJson})
                        \">
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=7DbfyX80LGwU&format=png&color=FFFFFF' alt=''>
                        Hapus
                    </button>
                </div>
            ";
            })
            ->rawColumns(['calibration', 'photo', 'action', 'procurement'])
            ->make(true);
    }


    public function otherBranch()
    {
        $branches = BranchOffice::all();

        return view('pages.admin.assets-other', [
            'branches' => $branches
        ]);
    }

    public function getAssetsOtherBranch(Request $request)
    {
        Carbon::setLocale('id');

        $branch = $request->query('branch_id');

        $assets = Asset::query();

        if ($branch) {
            $assets->where('branch_id', $branch);
        }

        $assets = $assets->get();

        return DataTables::of($assets)
            ->addIndexColumn()
            ->addColumn('brand', function ($asset) {
                return $asset->brand->name;
            })
            ->addColumn('calibration', function ($asset) {
                $calibrationFiles = json_decode($asset->calibration, true);
                $buttons = "<div class='flex gap-2'>";

                foreach ($calibrationFiles as $file) {
                    $buttons .= "
                    <div class='flex items-center gap-2'>
                        <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $file['path']) . "' target='_blank'>
                            <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                            " . $file['name'] . "
                        </a>
                    </div>";
                }

                $buttons .= "</div>";
                return $buttons;
            })
            ->addColumn('procurement', function ($asset) {
                return "
                <div class=''>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $asset->procurement) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                        Lihat
                    </a>
                </div>";
            })
            ->addColumn('photo', function ($asset) {
                return "
                <div class=''>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $asset->photo) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=VNxIqSP5pHwD&format=png&color=FFFFFF' alt=''>
                        Lihat
                    </a>
                </div>";
            })
            ->rawColumns(['calibration', 'photo', 'procurement'])
            ->make(true);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Brand;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'name' => 'required',
            'serial_number' => 'required',
            'brand_id' => 'required',
            'calibration' => 'required',
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
        $asset->name = $request->name;
        $asset->serial_number = $request->serial_number;
        $asset->brand_id = $request->brand_id;
        $asset->calibration = $request->calibration;

        $photo = $request->file('photo');
        $photoName = time() . '.' . $photo->extension();

        $photoPath = $photo->storeAs('assets', $photoName, 'public');
        $asset->photo = 'assets/' . $photoName;

        $file = $request->file('calibration');
        $fileName = time() . '.' . $file->extension();

        $filePath = $file->storeAs('assets', $fileName, 'public');
        $asset->calibration = 'assets/' . $fileName;

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

            $photoPath = $photo->storeAs('assets', $photoName, 'public');
            $asset->photo = 'assets/' . $photoName;
        }

        if ($request->hasFile('calibration')) {
            Storage::disk('public')->delete($asset->calibration);

            $file = $request->file('calibration');
            $fileName = time() . '.' . $file->extension();

            $filePath = $file->storeAs('assets', $fileName, 'public');
            $asset->calibration = 'assets/' . $fileName;
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
                return "
                <div class='flex w-full justify-center items-center gap-2'>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $asset->calibration) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                        Unduh
                    </a>
                </div>";
            })
            ->addColumn('photo', function ($asset) {
                return "
                <div class='flex w-full justify-center items-center gap-2'>
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
            ->rawColumns(['calibration', 'photo', 'action'])
            ->make(true);
    }
}

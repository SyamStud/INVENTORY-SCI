<?php

namespace App\Http\Controllers;

use App\Models\LoanAsset;
use App\Models\ReturnAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReturnAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.main.return');
    }

    public function storeReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assets' => 'required|array',
            'assets.*.loan_asset_id' => 'required|exists:loan_assets,id',
            'assets.*.return_check' => 'required|in:baik,rusak',
            'assets.*.notes' => 'nullable|string'
        ], [
            'assets.required' => 'Kondisi pengembalian tidak boleh kosong',
            'assets.*.return_check.required' => 'Kondisi pengembalian tidak boleh kosong',
            'assets.*.return_check.in' => 'Kondisi pengembalian tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        foreach ($request->assets as $asset) {
            $loanAsset = LoanAsset::find($asset['loan_asset_id']);

            if (!$asset['return_check']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kondisi pengembalian tidak boleh kosong'
                ]);
            }

            $loanAsset->update([
                'return_check' => $asset['return_check'],
                'notes' => $asset['notes']
            ]);
        }

        $loanAsset = LoanAsset::find($request->assets[0]['loan_asset_id']);
        $loanAsset->loan->update([
            'status' => 'returned'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'loan_id' => $loanAsset->loan->id
        ]);
    }
}

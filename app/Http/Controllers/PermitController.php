<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PermitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.main.permits');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'number' => 'required',
            'institution' => 'required',
            'due_date' => 'required',
            'file' => 'nullable|file|mimes:pdf',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $isExist = Permit::where('number', $request->number)->where('branch_id', Auth::user()->branch_id)->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor Dokumen sudah terdaftar',
            ]);
        }

        $permit = Permit::create([
            'name' => $request->name,
            'number' => $request->number,
            'institution' => $request->institution,
            'due_date' => $request->due_date,
            'branch_id' => Auth::user()->branch_id,
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assets/permits', $fileName, 'public');

            $permit->update([
                'file' => $path,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Perizinan berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permit $permit)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'number' => 'required',
            'institution' => 'required',
            'due_date' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $permit->update([
            'name' => $request->name,
            'name' => $request->name,
            'number' => $request->number,
            'institution' => $request->institution,
            'due_date' => $request->due_date,
        ]);

        if ($request->hasFile('file')) {
            if ($permit->file) {
                Storage::disk('public')->delete($permit->file);
            }

            $file = $request->file('file');

            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assets/permits', $fileName, 'public');

            $permit->update([
                'file' => $path,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Perizinan berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permit $permit)
    {
        $permit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Perizinan berhasil dihapus',
        ]);
    }

    /**
     * Get data for datatables.
     */

    public function getPermits()
    {
        $permits = Permit::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($permits)
            ->addIndexColumn()
            ->addColumn('file', function ($asset) {
                return "
                <div class='flex justify-center'>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $asset->file) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                        Unduh
                    </a>
                </div>";
            })
            ->addColumn('action', function ($permit) {
                $permitJson = htmlspecialchars(json_encode($permit), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-permit');
                            \$dispatch('set-permit', {$permitJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-permit');
                            \$dispatch('set-permit', {$permitJson})
                        \">
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=7DbfyX80LGwU&format=png&color=FFFFFF' alt=''>
                        Hapus
                    </button>
                </div>
            ";
            })
            ->rawColumns(['action', 'file'])
            ->make(true);
    }
}

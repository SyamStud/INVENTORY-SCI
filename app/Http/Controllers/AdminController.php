<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BranchOffice;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = BranchOffice::all();

        return view('pages.superAdmin.admins', [
            'branches' => $branches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'branch_id' => 'required|exists:branch_offices,id',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'branch_id' => $request->branch_id,
        ]);

        $admin->assignRole('admin');

        return response()->json([
            'status' => 'success',
            'message' => 'Admin berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'branch_id' => 'required|exists:branch_offices,id',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'branch_id' => $request->branch_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Admin berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Admin berhasil dihapus',
        ]);
    }

    /**
     * Get data for datatables.
     */

    public function getAdmins()
    {
        $users = User::with('branchOffice')
            ->whereHas('roles', function ($query) {
            $query->where('name', 'admin');
            })
            ->orderByDesc('created_at');

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('branch', function ($user) {
                return $user->branchOffice->name;
            })
            ->addColumn('action', function ($user) {
                $userJson = htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-admin');
                            \$dispatch('set-admin', {$userJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-admin');
                            \$dispatch('set-admin', {$userJson})
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

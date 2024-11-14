<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.admin.users', [
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'employee_id' => 'required|integer',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $employee = Employee::find($request->employee_id);

        $user = User::create([
            'name' => $employee->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'employee_id' => $request->employee_id,
            'branch_id' => Auth::user()->branch_id,
        ]);

        $user->assignRole('employee');

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $validation = Validator::make($request->all(), [
            'employee_id' => 'required|integer',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $user->update([
            'employee_id' => $request->employee_id,
            'email' => $request->email,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil dihapus',
        ]);
    }

    /**
     * Get data for datatables.
     */

    public function getUsers()
    {
        $users = User::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('employee', function ($user) {
                return $user->employee ? $user->employee->name : 'Admin';
            })
            ->addColumn('npp', function ($user) {
                return $user->employee ? $user->employee->npp : 'Admin';
            })
            ->addColumn('action', function ($user) {
                $userJson = htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-user');
                            \$dispatch('set-user', {$userJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-user');
                            \$dispatch('set-user', {$userJson})
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

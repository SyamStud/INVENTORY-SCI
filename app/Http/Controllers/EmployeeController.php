<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::where('branch_id', Auth::user()->branch_id)->get();
        $headOffice = Employee::where('branch_id', Auth::user()->branch_id)->whereHas('position', function ($query) {
            $query->where('name', 'Kepala Cabang');
        })->first();

        return view('pages.admin.employees', [
            'positions' => $positions,
            'headOffice' => $headOffice,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'npp' => 'required',
            'position_id' => 'required|exists:positions,id',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $isExist = Employee::where('npp', $request->npp)->where('branch_id', Auth::user()->branch_id)->first();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'NPP sudah terdaftar',
            ]);
        }

        Employee::create([
            'name' => $request->name,
            'npp' => $request->npp,
            'position_id' => $request->position_id,
            'branch_id' => Auth::user()->branch_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pegawai berhasil ditambahkan',
        ]);
    }

    public function storeHeadOffice(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'npp' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $isExist = Employee::where('npp', $request->npp)->where('branch_id', Auth::user()->branch_id)->first();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'NPP sudah terdaftar',
            ]);
        }

        $headOffice = Employee::where('branch_id', Auth::user()->branch_id)->whereHas('position', function ($query) {
            $query->where('name', 'Kepala Cabang');
        })->first();

        if (!$headOffice) {
            $position = Position::create([
                'name' => 'Kepala Cabang',
                'branch_id' => Auth::user()->branch_id,
            ]);

            Employee::create([
                'name' => $request->name,
                'npp' => $request->npp,
                'position_id' => $position->id,
                'branch_id' => Auth::user()->branch_id,
            ]);
        } else {
            $headOffice->update([
                'name' => $request->name,
                'npp' => $request->npp,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pegawai berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'npp' => 'required',
            'position_id' => 'required|exists:positions,id',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $employee->update([
            'npp' => $request->npp,
            'name' => $request->name,
            'position_id' => $request->position_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pegawai berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pegawai berhasil dihapus',
        ]);
    }

    /**
     * Get all employees
     */
    public function getEmployees()
    {
        $employees = Employee::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($employees)
            ->addIndexColumn()
            ->addColumn('position', function ($employee) {
                return $employee->position->name;
            })
            ->addColumn('action', function ($employee) {
                $employeeJson = htmlspecialchars(json_encode($employee), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-employee');
                            \$dispatch('set-employee', {$employeeJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-employee');
                            \$dispatch('set-employee', {$employeeJson})
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

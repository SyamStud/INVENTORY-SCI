<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        $users = User::with('permissions')->where('branch_id', Auth::user()->branch_id)->get();
        $permissions = \Spatie\Permission\Models\Permission::all();

        return view('pages.admin.permissions', compact('users', 'permissions'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'array',
            ]);

            $user = User::find($request->user_id);

            $user->syncPermissions($request->permissions ?? []);
            Log::info('User permissions updated', ['user' => $user->id, 'permissions' => $request->permissions]);

            return redirect()->back()->with('success', 'Hak Akses berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function getUserPermissions(User $user)
    {
        $permissions = $user->permissions->pluck('name');
        return response()->json($permissions);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Procurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ProcurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.main.procurements');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'user_name' => 'required|string',
            'status' => 'required|in:proses-anggaran,proses-pengadaan,penerbitan-po,sudah-diterima',
            'invoice_status' => 'required|in:belum-invoice,sudah-invoice',
            'payment_status' => 'required|in:belum-dibayar,sudah-dibayar',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        Procurement::create([
            'entry_date' => now(),
            'name' => $request->name,
            'user_name' => $request->user_name,
            'status' => $request->status,
            'process_date' => now(),
            'invoice_status' => $request->invoice_status,
            'invoice_date' => $request->invoice_date ? $request->invoice_date : null,
            'payment_status' => $request->payment_status,
            'payment_date' => $request->payment_date ? $request->payment_date : null,
            'branch_id' => Auth::user()->branch_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengadaan berhasil ditambahkan'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Procurement $procurement)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'user_name' => 'required|string',
            'status' => 'required|in:proses-anggaran,proses-pengadaan,penerbitan-po,sudah-diterima',
            'invoice_status' => 'required|in:belum-invoice,sudah-invoice',
            'payment_status' => 'required|in:belum-dibayar,sudah-dibayar',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        if ($request->status != $procurement->status) {
            $process_date = now();
        }

        $procurement->update([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'status' => $request->status,
            'process_date' => $process_date ?? $procurement->process_date,
            'invoice_status' => $request->invoice_status,
            'payment_status' => $request->payment_status,
        ]);

        if ($request->invoice_status === 'sudah-invoice') {
            $procurement->update([
                'invoice_date' => now()
            ]);
        } else {
            $procurement->update([
                'invoice_date' => null
            ]);
        }

        if ($request->payment_status === 'sudah-dibayar') {
            $procurement->update([
                'payment_date' => now()
            ]);
        } else {
            $procurement->update([
                'payment_date' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengadaan berhasil diperbarui'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Procurement $procurement)
    {
        $procurement->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengadaan berhasil dihapus'
        ]);
    }

    public function getProcurements()
    {
        Carbon::setLocale('id');

        $procurements = Procurement::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($procurements)
            ->addIndexColumn()
            ->addColumn('status', function ($procurement) {
                return match ($procurement->status) {
                    'proses-anggaran' => '<span style="background-color: #CC8400; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Proses Anggaran</span>',
                    'proses-pengadaan' => '<span style="background-color: #1A78D5; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Proses Pengadaan</span>',
                    'penerbitan-po' => '<span style="background-color: #2AA12A; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Penerbitan PO</span>',
                    'sudah-diterima' => '<span style="background-color: #666666; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Sudah Diterima</span>',
                    default => '<span style="background-color: #CC3700; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">-</span>',
                };
            })
            ->addColumn('invoice_status', function ($procurement) {
                return match ($procurement->invoice_status) {
                    'belum-invoice' => '<span style="background-color: #CC3700; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Belum Invoice</span>',
                    'sudah-invoice' => '<span style="background-color: #2AA12A; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Sudah Invoice</span>',
                    default => '<span style="background-color: #CC3700; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">-</span>',
                };
            })
            ->addColumn('payment_status', function ($procurement) {
                return match ($procurement->payment_status) {
                    'belum-dibayar' => '<span style="background-color: #CC3700; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Belum Dibayar</span>',
                    'sudah-dibayar' => '<span style="background-color: #2AA12A; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">Sudah Dibayar</span>',
                    default => '<span style="background-color: #CC3700; display: inline-block; width: 150px;" class="px-2 py-1 text-sm text-white rounded-md">-</span>',
                };
            })
            ->addColumn('entry_date', function ($procurement) {
                return Carbon::parse($procurement->entry_date)->translatedFormat('d F Y');
            })
            ->addColumn('process_date', function ($procurement) {
                return Carbon::parse($procurement->process_date)->translatedFormat('d F Y');
            })
            ->addColumn('invoice_date', function ($procurement) {
                return $procurement->invoice_date ? Carbon::parse($procurement->invoice_date)->translatedFormat('d F Y') : '-';
            })
            ->addColumn('payment_date', function ($procurement) {
                return $procurement->payment_date ? Carbon::parse($procurement->payment_date)->translatedFormat('d F Y') : '-';
            })
            ->addColumn('action', function ($procurement) {
                $procurementJson = htmlspecialchars(json_encode($procurement), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-procurement');
                            \$dispatch('set-procurement', {$procurementJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-procurement');
                            \$dispatch('set-procurement', {$procurementJson})
                        \">
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=7DbfyX80LGwU&format=png&color=FFFFFF' alt=''>
                        Hapus
                    </button>
                </div>
            ";
            })
            ->rawColumns(['action', 'status', 'invoice_status', 'payment_status'])
            ->make(true);
    }
}

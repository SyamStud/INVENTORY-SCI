<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    public function index()
    {
        $brands = Loan::where('branch_id', Auth::user()->branch_id)
            ->where('status', 'returned')
            ->get();

        return view('pages.admin.returns', [
            'brands' => $brands
        ]);
    }

    public function getReturn()
    {
        $loans = Loan::where('branch_id', Auth::user()->branch_id)
            ->where('status', 'returned')
            ->with('assets.asset')->orderByDesc('created_at');

        return DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('operation_head', function ($loans) {
                return $loans->user->name;
            })
            ->addColumn('loan_officer', function ($loans) {
                return $loans->user->name;
            })
            ->addColumn('general_division', function ($loans) {
                return $loans->generalDivision->name;
            })
            ->addColumn('created_at', function ($loans) {
                return $loans->created_at->format('d F Y');
            })
            ->addColumn('status', function ($loan) {
                // Definisikan mapping posisi
                $positionMapping = [
                    'loan_officer' => 'PETUGAS PINJAMAN',
                    'operation_head' => 'KEPALA BIDANG OPERASI',
                    'general_division' => 'BAGIAN UMUM'
                ];

                $missingSignatures = [];

                // Cek setiap posisi yang perlu tanda tangan
                foreach ($positionMapping as $field => $position) {
                    if ($loan->$field == Auth::user()->employee_id) {
                        $hasSignature = $loan->signatures()
                            ->where('position', $position)
                            ->exists();

                        if (!$hasSignature) {
                            $missingSignatures[] = $position;
                        }
                    }
                }

                // Jika status masih pending
                if ($loan->status == 'pending') {
                    $statusHtml = "<div class='flex items-center gap-2'>";

                    if (!empty($missingSignatures)) {
                        // Tampilkan status menunggu tanda tangan
                        $statusHtml .= "<span style='background-color: #ca8a04' class='text-sm px-2 py-1 text-white bg-yellow-600 rounded-md'>Menunggu Tanda Tangan</span>";

                        // Tampilkan posisi yang belum tanda tangan
                        foreach ($missingSignatures as $position) {
                            $statusHtml .= "<span style='background-color: #dc2626' class='text-sm px-2 py-1 text-white bg-red-600 rounded-md'>{$position}</span>";
                        }
                    } else {
                        // Jika semua posisi sudah tanda tangan
                        $statusHtml .= "<span style='background-color: #16a34a' class='text-sm px-2 py-1 text-white bg-green-600 rounded-md'>Sudah Ditandatangani</span>";
                    }

                    $statusHtml .= "</div>";

                    return $statusHtml;
                } else if ($loan->status == 'on_loan') {
                    return "<div class='flex items-center gap-2'>
                                <span style='background-color: #133E87' class='text-sm px-3 py-1 text-white bg-blue-700 rounded-md font-medium'>Dalam Peminjaman</span>
                            </div>";
                } else {
                    return "<div class='flex items-center gap-2'>
                                <span style='background-color: #15803d' class='text-sm px-2 py-1 text-white bg-green-700 rounded-md'>Telah Dikembalikan</span>
                            </div>";
                }
            })
            ->addColumn('document', function ($loans) {
                // Definisikan mapping posisi
                $positionMapping = [
                    'loan_officer' => 'PETUGAS PINJAMAN',
                    'operation_head' => 'KEPALA BIDANG OPERASI',
                    'general_division' => 'BAGIAN UMUM'
                ];

                $missingSignatures = [];

                // Cek setiap posisi yang perlu tanda tangan
                foreach ($positionMapping as $field => $position) {
                    if ($loans->$field == Auth::user()->employee_id) {
                        $hasSignature = $loans->signatures()
                            ->where('position', $position)
                            ->exists();

                        if (!$hasSignature) {
                            $missingSignatures[] = $position;
                        }
                    }
                }

                // Jika status masih pending
                if ($loans->status == 'pending') {
                    $statusHtml = "<div class='flex items-center gap-2'>";

                    if (!empty($missingSignatures)) {
                        // Tampilkan status menunggu tanda tangan
                        $statusHtml .= "<span style='background-color: #ca8a04' class='px-2 py-1 text-white bg-yellow-600 rounded-md'>Menunggu Tanda Tangan</span>";
                    } else {
                        // Jika semua posisi sudah tanda tangan
                        $statusHtml .= "<a href='/documents/returns/download/{$loans->id}/true/" . str_replace('/', '-', $loans->loan_number) . "' target='_blank' class='w-max flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' style='background-color: #133E87;'>
                                            <img class='w-5' src='https://img.icons8.com/?size=100&id=9ZFMqzgXThYz&format=png&color=FFFFFF' alt=''>
                                            Lihat Dokumen
                                        </a>";
                    }

                    $statusHtml .= "</div>";

                    return $statusHtml;
                }

                return "<a href='/documents/returns/download/{$loans->id}/true/" . str_replace('/', '-', $loans->loan_number) . "' target='_blank' class='w-max flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' style='background-color: #133E87;'>
                    <img class='w-5' src='https://img.icons8.com/?size=100&id=VNxIqSP5pHwD&format=png&color=FFFFFF' alt=''>
                    Lihat Dokumen
                </a>";
            })
            ->addColumn('detail', function ($loans) {
                $loansJson = htmlspecialchars(json_encode($loans), ENT_QUOTES, 'UTF-8');

                return "
                    <div class='flex items-center gap-2 pb-1'>
                        <button style='background-color: #133E87;' 
                            class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                            x-data='' 
                            x-on:click.prevent=\"
                                \$dispatch('open-modal', 'detail-loan');
                                \$dispatch('set-asset', {$loansJson})
                            \">
                            <img class='w-5' src='https://img.icons8.com/?size=100&id=VNxIqSP5pHwD&format=png&color=FFFFFF' alt=''>
                            Lihat
                        </button>
                    </div>
                ";
            })
            ->rawColumns(['detail', 'status', 'document'])
            ->make(true);
    }

    public function getReturnAssets(Request $request)
    {
        $loanAssets = LoanAsset::where('loan_id', $request->loan_id)
            ->with('asset')
            ->get();

        return DataTables::of($loanAssets)
            ->addIndexColumn()
            ->addColumn('nama_asset', function ($loanAsset) {
                return $loanAsset->asset->name;
            })
            ->addColumn('duration', function ($loanAsset) {
                return $loanAsset->duration . ' hari';
            })
            ->make(true);
    }
}

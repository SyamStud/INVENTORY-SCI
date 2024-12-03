<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Permit;
use App\Models\Procurement;
use App\Models\BranchOffice;
use App\Models\LoanAsset;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MonitoringController extends Controller
{
    public function procurementIndex()
    {
        $branches = BranchOffice::all();

        return view('pages.employee.monitoring.procurements', [
            'branches' => $branches
        ]);
    }

    public function permitIndex()
    {
        $branches = BranchOffice::all();

        return view('pages.employee.monitoring.permits', [
            'branches' => $branches
        ]);
    }

    public function assetIndex()
    {
        $branches = BranchOffice::all();

        return view('pages.employee.monitoring.assets', [
            'branches' => $branches
        ]);
    }

    public function loanAssetIndex()
    {
        $branches = BranchOffice::all();

        return view('pages.employee.monitoring.loanAssets', [
            'branches' => $branches
        ]);
    }

    public function procurementData(Request $request)
    {
        $branch = $request->query('branch_id');

        $procurements = Procurement::query();

        if ($branch) {
            $procurements->where('branch_id', $branch);
        }

        $procurements = $procurements->get();

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
            ->rawColumns(['status', 'invoice_status', 'payment_status'])
            ->make(true);
    }

    public function permitData(Request $request)
    {
        $branch = $request->query('branch_id');

        $permits = Permit::query();

        if ($branch) {
            $permits->where('branch_id', $branch);
        }

        $permits = $permits->get();

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
            ->addColumn('due_date', function ($permit) {
                return Carbon::parse($permit->due_date)->translatedFormat('d F Y');
            })
            ->rawColumns(['action', 'file'])
            ->make(true);
    }

    public function assetData(Request $request)
    {
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

    public function loanAssetData(Request $request)
    {
        $branch = $request->query('branch_id');

        $loanAssets = LoanAsset::query();

        if ($branch) {
            $loanAssets->where('branch_id', $branch);
        }

        $loanAssets = $loanAssets->get();

        return DataTables::of($loanAssets)
            ->addIndexColumn()
            ->addColumn('loan_number', function ($loanAsset) {
                return $loanAsset->loan->loan_number;
            })
            ->addColumn('customer_name', function ($loanAsset) {
                return $loanAsset->loan->customer_name;
            })
            ->addColumn('tag_number', function ($loanAsset) {
                return $loanAsset->asset->tag_number;
            })
            ->addColumn('serial_number', function ($loanAsset) {
                return $loanAsset->asset->serial_number;
            })
            ->addColumn('asset', function ($loanAsset) {
                return $loanAsset->asset->name;
            })
            ->addColumn('brand', function ($loanAsset) {
                return $loanAsset->asset->brand->name;
            })
            ->addColumn('duration', function ($loanAsset) {
                return $loanAsset->duration . ' hari';
            })
            ->addColumn('return_estimation', function ($loanAsset) {
                return $loanAsset->created_at->addDays($loanAsset->duration)->translatedFormat('d F Y');
            })
            ->addColumn('created_at', function ($loanAsset) {
                return $loanAsset->created_at->translatedFormat('d F Y');
            })
            ->make(true);
    }
}

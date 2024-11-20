<?php

namespace App\Http\Controllers;

use App\Models\Outbound;
use App\Models\OutboundItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class OutboundController extends Controller
{
    public function index()
    {
        $brands = Outbound::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.admin.outbounds', [
            'brands' => $brands
        ]);
    }

    public function getOutbound()
    {
        $outbounds = Outbound::where('branch_id', Auth::user()->branch_id)->with('items.item')->orderByDesc('created_at');

        return DataTables::of($outbounds)
            ->addIndexColumn()
            ->addColumn('approved_by', function ($outbounds) {
                return $outbounds->approvedBy->name;
            })
            ->addColumn('released_by', function ($outbounds) {
                return $outbounds->releasedBy->name;
            })
            ->addColumn('created_at', function ($outbounds) {
                return $outbounds->created_at->format('d F Y');
            })
            ->addColumn('status', function ($outbound) {
                // Definisikan mapping posisi
                $positionMapping = [
                    'received_by' => 'PENERIMA',
                    'approved_by' => 'PENGESAH',
                    'released_by' => 'PENANGGUNG JAWAB',
                ];

                $missingSignatures = [];

                // Cek setiap posisi yang perlu tanda tangan
                foreach ($positionMapping as $field => $position) {
                    if ($field == 'received_by') {
                        $hasSignature = $outbound->signatures()
                            ->where('position', 'PENERIMA')
                            ->exists();

                        if (!$hasSignature) {
                            $missingSignatures[] = 'PENERIMA';
                        }
                    } else {
                        $hasSignature = $outbound->signatures()
                            ->where('position', $position)
                            ->exists();

                        if (!$hasSignature) {
                            $missingSignatures[] = $position;
                        }
                    }
                }

                // Jika status masih pending
                if ($outbound->status == 'pending') {
                    $statusHtml = "<div class='flex items-center gap-2'>";

                    if (!empty($missingSignatures)) {
                        // Tampilkan status menunggu tanda tangan
                        $statusHtml .= "<span style='background-color: #ca8a04' class='px-2 py-1 text-white bg-yellow-600 rounded-md'>Menunggu Tanda Tangan</span>";

                        // Tampilkan posisi yang belum tanda tangan
                        foreach ($missingSignatures as $position) {
                            if ($position == 'PENERIMA') {
                                $statusHtml .= "<a href='/outbounds/sign/{$outbound->id}' class='flex gap-2 items-center px-2 py-1 text-white rounded-md' style='background-color: #133E87;'>
                                                    <img class='w-4' src='https://img.icons8.com/?size=100&id=9ZFMqzgXThYz&format=png&color=FFFFFF' alt=''>
                                                    {$position}
                                                </a>";
                            } else {
                                $statusHtml .= "<span style='background-color: #dc2626' class='px-2 py-1 text-white bg-red-600 rounded-md'>{$position}</span>";
                            }
                        }
                    } else {
                        // Jika semua posisi sudah tanda tangan
                        $statusHtml .= "<span style='background-color: #16a34a' class='px-2 py-1 text-white bg-green-600 rounded-md'>Sudah Ditandatangani</span>";
                    }

                    $statusHtml .= "</div>";

                    return $statusHtml;
                } else {
                    return "<div class='flex items-center gap-2'>
                                <span style='background-color: #15803d' class='px-2 py-1 text-white bg-green-700 rounded-md'>Disetujui</span>
                            </div>";
                }
            })
            ->addColumn('document', function ($outbounds) {
                // Definisikan mapping posisi
                $positionMapping = [
                    'approved_by' => 'PENGESAH',
                    'released_by' => 'PENANGGUNG JAWAB',
                    'received_by' => 'PENERIMA'
                ];

                $missingSignatures = [];

                // Cek setiap posisi yang perlu tanda tangan
                foreach ($positionMapping as $field => $position) {
                    if ($field == 'received_by') {
                        $hasSignature = $outbounds->signatures()
                            ->where('position', 'PENERIMA')
                            ->exists();

                        if (!$hasSignature) {
                            $missingSignatures[] = 'PENERIMA';
                        }
                    } else {
                        $hasSignature = $outbounds->signatures()
                            ->where('position', $position)
                            ->exists();

                        if (!$hasSignature) {
                            $missingSignatures[] = $position;
                        }
                    }
                }

                // Jika status masih pending
                if ($outbounds->status == 'pending') {
                    $statusHtml = "<div class='flex items-center gap-2'>";

                    if (!empty($missingSignatures)) {
                        // Tampilkan status menunggu tanda tangan
                        $statusHtml .= "<span style='background-color: #ca8a04' class='px-2 py-1 text-white bg-yellow-600 rounded-md'>Menunggu Tanda Tangan</span>";
                    } else {
                        // Jika semua posisi sudah tanda tangan
                        $statusHtml .= "<a href='/documents/outbounds/download/{$outbounds->id}/true' target='_blank' class='w-max flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' style='background-color: #133E87;'>
                                            <img class='w-5' src='https://img.icons8.com/?size=100&id=9ZFMqzgXThYz&format=png&color=FFFFFF' alt=''>
                                            Lihat Dokumen
                                        </a>";
                    }

                    $statusHtml .= "</div>";

                    return $statusHtml;
                }

                return "<a href='/documents/outbounds/download/{$outbounds->id}/true' target='_blank' class='w-max flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' style='background-color: #133E87;'>
                    <img class='w-5' src='https://img.icons8.com/?size=100&id=VNxIqSP5pHwD&format=png&color=FFFFFF' alt=''>
                    Lihat Dokumen
                </a>";
            })
            ->addColumn('detail', function ($outbounds) {
                $outboundsJson = htmlspecialchars(json_encode($outbounds), ENT_QUOTES, 'UTF-8');

                return "
                    <div class='flex items-center gap-2 pb-1'>
                        <button style='background-color: #133E87;' 
                            class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                            x-data='' 
                            x-on:click.prevent=\"
                                \$dispatch('open-modal', 'detail-outbound');
                                \$dispatch('set-item', {$outboundsJson})
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

    public function getOutboundItems(Request $request)
    {
        $outboundItems = OutboundItem::where('outbound_id', $request->outbound_id)
            ->with('item')
            ->get();

        return DataTables::of($outboundItems)
            ->addIndexColumn()
            ->addColumn('name', function ($outboundItem) {
                return $outboundItem->item->name;
            })
            ->addColumn('quantity', function ($outboundItem) {
                return $outboundItem->quantity . ' ' . $outboundItem->item->unit->name;
            })
            ->addColumn('price', function ($outboundItem) {
                return 'Rp' . number_format($outboundItem->price, 0, ',', '.');
            })
            ->addColumn('total_price', function ($outboundItem) {
                $total = $outboundItem->price * $outboundItem->quantity;

                return 'Rp' . number_format($total, 0, ',', '.');
            })
            ->make(true);
    }
}

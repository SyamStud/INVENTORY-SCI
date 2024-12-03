<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Inbound;
use App\Models\Employee;
use App\Models\InboundItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class InboundController extends Controller
{
    public function index()
    {
        $brands = Inbound::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.admin.inbounds', [
            'brands' => $brands
        ]);
    }

    public function getInbound()
    {
        $inbounds = Inbound::where('branch_id', Auth::user()->branch_id)->with('items.item')->orderByDesc('created_at');

        return DataTables::of($inbounds)
            ->addIndexColumn()
            // ->addColumn('received_by', function ($inbound) {
            //     return $inbound->receivedBy;
            // })
            ->addColumn('created_at', function ($inbound) {
                return $inbound->created_at->format('d F Y');
            })
            // ->addColumn('status', function ($inbound) {
            //     // Definisikan mapping posisi
            //     $positionMapping = [
            //         'approved_by' => 'PENGESAH',
            //         'released_by' => 'PENANGGUNG JAWAB',
            //         'received_by' => 'PENERIMA'
            //     ];

            //     $missingSignatures = [];

            //     // Cek setiap posisi yang perlu tanda tangan
            //     foreach ($positionMapping as $field => $position) {
            //         if ($inbound->$field == Auth::user()->employee_id) {
            //             $hasSignature = $inbound->signatures()
            //                 ->where('position', $position)
            //                 ->exists();

            //             if (!$hasSignature) {
            //                 $missingSignatures[] = $position;
            //             }
            //         }
            //     }

            //     // Jika status masih pending
            //     if ($inbound->status == 'pending') {
            //         $statusHtml = "<div class='flex items-center gap-2'>";

            //         if (!empty($missingSignatures)) {
            //             // Tampilkan status menunggu tanda tangan
            //             $statusHtml .= "<span style='background-color: #ca8a04' class='px-2 py-1 text-white bg-yellow-600 rounded-md'>Menunggu Tanda Tangan</span>";

            //             // Tampilkan posisi yang belum tanda tangan
            //             foreach ($missingSignatures as $position) {
            //                 $statusHtml .= "<span style='background-color: #dc2626' class='px-2 py-1 text-white bg-red-600 rounded-md'>{$position}</span>";
            //             }
            //         } else {
            //             // Jika semua posisi sudah tanda tangan
            //             $statusHtml .= "<span style='background-color: #16a34a' class='px-2 py-1 text-white bg-green-600 rounded-md'>Sudah Ditandatangani</span>";
            //         }

            //         $statusHtml .= "</div>";

            //         return $statusHtml;
            //     } else {
            //         return "<div class='flex items-center gap-2'>
            //                     <span style='background-color: #15803d' class='px-2 py-1 text-white bg-green-700 rounded-md'>Disetujui</span>
            //                 </div>";
            //     }
            // })
            ->addColumn('detail', function ($inbounds) {
                $inboundsJson = htmlspecialchars(json_encode($inbounds), ENT_QUOTES, 'UTF-8');

                return "
                    <div class='flex items-center gap-2 pb-1'>
                        <button style='background-color: #133E87;' 
                            class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                            x-data='' 
                            x-on:click.prevent=\"
                                \$dispatch('open-modal', 'detail-inbound');
                                \$dispatch('set-item', {$inboundsJson})
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

    public function getInboundItems(Request $request)
    {
        $inboundItems = InboundItem::where('inbound_id', $request->inbound_id)
            ->with('item')
            ->get();

        return DataTables::of($inboundItems)
            ->addIndexColumn()
            ->addColumn('name', function ($inboundItem) {
                return $inboundItem->item->name;
            })
            ->addColumn('quantity', function ($inboundItem) {
                return $inboundItem->quantity . ' ' . $inboundItem->item->unit->name;
            })
            ->addColumn('cost', function ($inboundItem) {
                return 'Rp ' . number_format($inboundItem->cost, 0, ',', '.');
            })
            ->addColumn('total_cost', function ($inboundItem) {
                $total = $inboundItem->cost * $inboundItem->quantity;

                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->make(true);
    }
}

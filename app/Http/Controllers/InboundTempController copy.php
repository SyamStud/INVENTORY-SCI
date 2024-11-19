<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Inbound;
use App\Models\Employee;
use App\Models\InboundItem;
use App\Models\InboundTemp;
use Illuminate\Http\Request;
use App\Models\InboundItemTemp;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class InboundTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inbound = InboundTemp::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->first();

        $items = Item::where('branch_id', Auth::user()->branch_id)->get();
        $employees = Employee::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.main.inbound', [
            'items' => $items,
            'employees' => $employees,
            'inbound' => $inbound
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'inbound_number' => 'required',
            'received_from' => 'required',
            'order_note_number' => 'required',
            'contract_note_number' => 'required',
            'delivery_note_number' => 'required',
            'item_id' => 'required',
            'quantity' => 'required',
            'cost' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $isExist = InboundTemp::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->exists();

        if (!$isExist) {
            $inbound = InboundTemp::create([
                'inbound_number' => $request->inbound_number,
                'received_from' => $request->received_from,
                'order_note_number' => $request->order_note_number,
                'contract_note_number' => $request->contract_note_number,
                'delivery_note_number' => $request->delivery_note_number,
                'date_received' => now(),
                'total_cost' => 0,
                'branch_id' => Auth::user()->branch_id,
                'user_id' => Auth::id(),
            ]);
        } else {
            $inbound = InboundTemp::where('branch_id', Auth::user()->branch_id)
                ->where('user_id', Auth::user()->id)
                ->first();

            $inbound->update([
                'inbound_number' => $request->inbound_number,
                'received_from' => $request->received_from,
                'order_note_number' => $request->order_note_number,
                'contract_note_number' => $request->contract_note_number,
                'delivery_note_number' => $request->delivery_note_number,
            ]);
        }

        $totalCost = 0;

        $isExist = InboundItemTemp::where('inbound_temp_id', $inbound->id)
            ->where('item_id', $request->item_id)
            ->where('cost', $request->cost)
            ->where('branch_id', Auth::user()->branch_id)
            ->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang sudah ada'
            ]);
        }

        $inbound->items()->create([
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'cost' => $request->cost,
            'branch_id' => Auth::user()->branch_id,
        ]);

        $totalCost += $request->quantity * $request->cost;

        $inbound->update(['total_cost' => $totalCost]);

        return response()->json([
            'status' => 'success',
            'message' => 'Inbound record created successfully',
            'inbound' => $inbound
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inboundItemTemp = InboundItemTemp::where('id', $id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        Log::info($inboundItemTemp);
        Log::info($id);

        $validation = Validator::make($request->all(), [
            'inbound_item_id' => 'required',
            'quantity' => 'required',
            'cost' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $inboundItemTemp->update([
            'quantity' => $request->quantity,
            'cost' => $request->cost,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Inbound record updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inboundItemTemp = InboundItemTemp::where('id', $id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $inboundItemTemp->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Barang berhasil dihapus',
        ]);
    }

    public function cancelInbound(Request $request)
    {
        $inbound = InboundTemp::where('inbound_number', $request->inbound_number)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $inbound->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Penerimaan berhasil dibatalkan',
        ]);
    }

    public function storeInbound(Request $request)
    {
        $inboundTemp = InboundTemp::where('user_id', Auth::user()->id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $inbound = Inbound::create([
            'inbound_number' => $inboundTemp->inbound_number,
            'received_from' => $inboundTemp->received_from,
            'order_note_number' => $inboundTemp->order_note_number,
            'contract_note_number' => $inboundTemp->contract_note_number,
            'delivery_note_number' => $inboundTemp->delivery_note_number,
            'date_received' => $inboundTemp->date_received,
            'received_by' => $inboundTemp->received_by,
            'total_cost' => $inboundTemp->total_cost,
            'branch_id' => $inboundTemp->branch_id,
            'user_id' => $inboundTemp->user_id,
        ]);

        $inbound->items()->createMany(
            $inboundTemp->items->map(function ($item) use ($inbound) {
                return [
                    'inbound_id' => $inbound->id,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'cost' => $item->cost,
                    'branch_id' => $item->branch_id,
                ];
            })->toArray()
        );

        $inboundTemp->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Penerimaan berhasil dikonfirmasi',
            'inbound_id' => $inbound->id,
        ]);
    }

    public function checkInbound()
    {
        $inboundTemp = InboundTemp::where('user_id', Auth::user()->id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        if ($inboundTemp) {
            return response()->json([
                'exist' => true
            ]);
        } else {
            return response()->json([
                'exist' => false
            ]);
        }
    }

    public function receipt($id)
    {
        try {
            $inbound = Inbound::find($id);

            $phpWord = new TemplateProcessor('template.docx');

            // Set template values
            $phpWord->setValue('inbound_number', $inbound->inbound_number);
            $phpWord->setValue('created_at', $inbound->created_at->locale('id')->isoFormat('D MMMM YYYY'));
            $phpWord->setValue('received_from', $inbound->received_from);
            $phpWord->setValue('order_note_number', $inbound->order_note_number);
            $phpWord->setValue('contract_note_number', $inbound->contract_note_number);
            $phpWord->setValue('delivery_note_number', $inbound->delivery_note_number);
            $phpWord->setValue('date', now()->locale('id')->isoFormat('D MMMM YYYY'));

            // Process items
            $values = [];
            foreach ($inbound->items as $index => $inboundItem) {
                $total = $inboundItem->quantity * $inboundItem->cost;

                $values[$index] = [
                    'no' => $index + 1,
                    'item_name' => $inboundItem->item->name,
                    'quantity' => $inboundItem->quantity,
                    'cost' => $inboundItem->cost,
                    'total' => $total
                ];
            }

            $phpWord->cloneRowAndSetValues('no', $values);

            // Mengatur header untuk download
            header("Content-Description: File Transfer");
            header('Content-Disposition: attachment; filename="' . $inbound->inbound_number . '.docx"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Cache-Control: max-age=0');

            // Output ke browser
            $phpWord->saveAs('php://output');

            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getInbounds()
    {
        $inbounds = InboundItemTemp::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($inbounds)
            ->addIndexColumn()
            ->addColumn('item', function ($inbound) {
                return $inbound->item->name;
            })
            ->addColumn('action', function ($inbound) {
                $inboundJson = htmlspecialchars(json_encode($inbound), ENT_QUOTES, 'UTF-8');
                Log::info($inboundJson);
                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-item');
                            \$dispatch('set-item', {$inboundJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-item');
                            \$dispatch('set-item', {$inboundJson})
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

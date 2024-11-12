<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Inbound;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InboundController extends Controller
{
    public function index()
    {
        $items = Item::where('branch_id', Auth::user()->branch_id)->get();
        $employees = Employee::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.main.inbound', [
            'items' => $items,
            'employees' => $employees
        ]);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'inbound_number' => 'required',
            'received_from' => 'required',
            'order_note_number' => 'required',
            'contract_note_number' => 'required',
            'delivery_note_number' => 'required',
            'received_by' => 'required',
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

        $isExist = Inbound::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->exists();

        if (!$isExist) {
            $inbound = Inbound::create([
                'inbound_number' => $request->inbound_number,
                'received_from' => $request->received_from,
                'order_note_number' => $request->order_note_number,
                'contract_note_number' => $request->contract_note_number,
                'delivery_note_number' => $request->delivery_note_number,
                'date_received' => now(),
                'received_by' => $request->received_by,
                'total_cost' => 0,
                'branch_id' => Auth::user()->branch_id,
                'user_id' => Auth::id(),
            ]);
        } else {
            $inbound = Inbound::where('branch_id', Auth::user()->branch_id)
                ->where('user_id', Auth::user()->id)
                ->first();

            $inbound->update([
                'inbound_number' => $request->inbound_number,
                'received_from' => $request->received_from,
                'order_note_number' => $request->order_note_number,
                'contract_note_number' => $request->contract_note_number,
                'delivery_note_number' => $request->delivery_note_number,
                'received_by' => $request->received_by,
            ]);
        }

        $totalCost = 0;

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
}

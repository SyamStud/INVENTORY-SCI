<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\DeliveryOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DeliveryOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.main.deliveryOuts');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'resi' => 'required|string',
            'delivery_date' => 'required|date',
            'sender' => 'required|string',
            'receiver' => 'required|string',
            'received_date' => 'required|date',
            'received_by' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $deliveryOut = new DeliveryOut();

        $deliveryOut->resi = $request->resi;
        $deliveryOut->delivery_date = $request->delivery_date;
        $deliveryOut->sender = $request->sender;
        $deliveryOut->receiver = $request->receiver;
        $deliveryOut->received_date = $request->received_date;
        $deliveryOut->received_by = $request->received_by;
        $deliveryOut->branch_id = Auth::user()->branch_id;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->extension();
            $photoPath = $photo->storeAs('assets/deliveryOuts', $photoName, 'public');
            $deliveryOut->photo = 'assets/deliveryOuts/' . $photoName;
        }

        $deliveryOut->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryOut $deliveryOut)
    {
        $validation = Validator::make($request->all(), [
            'resi' => 'required|string',
            'delivery_date' => 'required|date',
            'sender' => 'required|string',
            'receiver' => 'required|string',
            'received_date' => 'required|date',
            'received_by' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
            ]);
        }

        $deliveryOut->resi = $request->resi;
        $deliveryOut->delivery_date = $request->delivery_date;
        $deliveryOut->sender = $request->sender;
        $deliveryOut->receiver = $request->receiver;
        $deliveryOut->received_date = $request->received_date;
        $deliveryOut->received_by = $request->received_by;

        if ($request->hasFile('photo')) {
            if ($deliveryOut->photo && file_exists(storage_path('app/public/assets/deliveryOuts/' . $deliveryOut->photo))) {
                unlink(storage_path('app/public/assets/deliveryOuts/' . $deliveryOut->photo));
            }

            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->extension();
            $photoPath = $photo->storeAs('assets/deliveryOuts', $photoName, 'public');

            $deliveryOut->photo = 'assets/deliveryOuts/' . $photoName;
        }

        $deliveryOut->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryOut $deliveryOut)
    {
        $deliveryOut->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }

    // Controller Method
    public function getDeliveryOuts()
    {
        Carbon::setLocale('id');

        $deliveryOuts = DeliveryOut::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($deliveryOuts)
            ->addIndexColumn()
            ->addColumn('delivery_date', function ($deliveryOut) {
                return Carbon::parse($deliveryOut->delivery_date)->translatedFormat('d F Y');
            })
            ->addColumn('received_date', function ($deliveryOut) {
                return Carbon::parse($deliveryOut->received_date)->translatedFormat('d F Y');
            })
            ->addColumn('photo', function ($deliveryOut) {
                return "
                <div class='flex justify-center'>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $deliveryOut->photo) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                        Lihat Foto
                    </a>
                </div>";
            })
            ->addColumn('action', function ($deliveryOut) {
                $deliveryOutJson = htmlspecialchars(json_encode($deliveryOut), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-delivery');
                            \$dispatch('set-delivery', {$deliveryOutJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-delivery');
                            \$dispatch('set-delivery', {$deliveryOutJson})
                        \">
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=7DbfyX80LGwU&format=png&color=FFFFFF' alt=''>
                        Hapus
                    </button>
                </div>
            ";
            })
            ->rawColumns(['action', 'photo'])->make(true);
    }
}

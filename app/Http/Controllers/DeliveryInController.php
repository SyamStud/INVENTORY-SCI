<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\DeliveryIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DeliveryInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.main.deliveryIns');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'date' => 'required|date',
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

        $deliveryIn = new DeliveryIn();

        $deliveryIn->date = $request->date;
        $deliveryIn->sender = $request->sender;
        $deliveryIn->receiver = $request->receiver;
        $deliveryIn->received_date = $request->received_date;
        $deliveryIn->received_by = $request->received_by;
        $deliveryIn->branch_id = Auth::user()->branch_id;

        $photo = $request->file('photo');
        $photoName = time() . '.' . $photo->extension();
        $photoPath = $photo->storeAs('assets/deliveryIns', $photoName, 'public');

        $deliveryIn->photo = 'assets/deliveryIns/' . $photoName;
        $deliveryIn->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryIn $deliveryIn)
    {
        $validation = Validator::make($request->all(), [
            'date' => 'required',
            'sender' => 'required',
            'receiver' => 'required',
            'received_date' => 'required',
            'received_by' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withInput()->withErrors($validation);
        }

        $deliveryIn->date = $request->date;
        $deliveryIn->sender = $request->sender;
        $deliveryIn->receiver = $request->receiver;
        $deliveryIn->received_date = $request->received_date;
        $deliveryIn->received_by = $request->received_by;

        if ($request->hasFile('photo')) {
            if ($deliveryIn->photo && file_exists(storage_path('app/public/assets/deliveryIns/' . $deliveryIn->photo))) {
                unlink(storage_path('app/public/assets/deliveryIns/' . $deliveryIn->photo));
            }

            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->extension();
            $photoPath = $photo->storeAs('assets/deliveryIns', $photoName, 'public');

            $deliveryIn->photo = 'assets/deliveryIns/' . $photoName;
        }

        $deliveryIn->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diubah',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryIn $deliveryIn)
    {
        $deliveryIn->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }

    // Controller Method
    public function getDeliveryIns()
    {
        Carbon::setLocale('id');

        $deliveryIns = DeliveryIn::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($deliveryIns)
            ->addIndexColumn()
            ->addColumn('date', function ($vehicleUsage) {
                return Carbon::parse($vehicleUsage->date)->translatedFormat('d F Y');
            })
            ->addColumn('received_date', function ($deliveryIn) {
                return Carbon::parse($deliveryIn->received_date)->translatedFormat('d F Y');
            })
            ->addColumn('photo', function ($deliveryIn) {
                return "
                <div class='flex justify-center'>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='" . asset('storage/' . $deliveryIn->photo) . "' target='_blank'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=CoWjc6xXzIS8&format=png&color=FFFFFF' alt=''>
                        Lihat Foto
                    </a>
                </div>";
            })
            ->addColumn('action', function ($deliveryIn) {
                $deliveryInJson = htmlspecialchars(json_encode($deliveryIn), ENT_QUOTES, 'UTF-8');

                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-delivery');
                            \$dispatch('set-delivery', {$deliveryInJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-delivery');
                            \$dispatch('set-delivery', {$deliveryInJson})
                        \">
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=7DbfyX80LGwU&format=png&color=FFFFFF' alt=''>
                        Hapus
                    </button>
                </div>
            ";
            })
            ->rawColumns(['action', 'photo'])
            ->make(true);
    }
}

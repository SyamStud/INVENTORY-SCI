<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use App\Models\Employee;
use App\Models\Outbound;
use App\Models\OutboundTemp;
use Illuminate\Http\Request;
use App\Models\OutboundItemTemp;
use App\Services\GotenbergService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class OutboundTempController extends Controller
{
    protected $gotenbergService;

    public function __construct(GotenbergService $gotenbergService)
    {
        $this->gotenbergService = $gotenbergService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outbound = OutboundTemp::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->first();

        $items = Item::where('branch_id', Auth::user()->branch_id)->get();
        $employees = Employee::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.main.outbound', [
            'items' => $items,
            'employees' => $employees,
            'outbound' => $outbound
        ]);
    }

    public function signOutbound($id)
    {
        $outbound = Outbound::find($id);

        return view('pages.main.signOutbound', [
            'outbound' => $outbound
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'outbound_number' => 'required',
                'release_to' => 'required',
                'release_reason' => 'required',
                'request_note_number' => 'required',
                'delivery_note_number' => 'required',
                'received_by' => 'required',
                'item_id' => 'required',
                'quantity' => 'required|numeric|min:1',
            ],
            [
                'quantity.min' => 'Jumlah barang minimal 1'
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $isExist = OutboundTemp::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->exists();

        if (!$isExist) {
            $outbound = OutboundTemp::create([
                'outbound_number' => $request->outbound_number,
                'release_to' => $request->release_to,
                'release_reason' => $request->release_reason,
                'request_note_number' => $request->request_note_number,
                'delivery_note_number' => $request->delivery_note_number,
                'date_released' => now(),
                'received_by' => $request->received_by,
                'total_price' => 0,
                'branch_id' => Auth::user()->branch_id,
                'user_id' => Auth::id(),
            ]);
        } else {
            $outbound = OutboundTemp::where('branch_id', Auth::user()->branch_id)
                ->where('user_id', Auth::user()->id)
                ->first();

            $outbound->update([
                'outbound_number' => $request->outbound_number,
                'release_to' => $request->release_to,
                'release_reason' => $request->release_reason,
                'request_note_number' => $request->request_note_number,
                'delivery_note_number' => $request->delivery_note_number,
                'received_by' => $request->received_by,
            ]);
        }

        $isExist = OutboundItemTemp::where('outbound_temp_id', $outbound->id)
            ->where('item_id', $request->item_id)
            ->where('branch_id', Auth::user()->branch_id)
            ->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang sudah ada'
            ]);
        }

        $item = Item::find($request->item_id);

        if ($item->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok barang hanya tersisa ' . $item->stock
            ]);
        }

        $outbound->items()->create([
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'price' => $item->price,
            'total_price' => $request->quantity * $item->price,
            'branch_id' => Auth::user()->branch_id,
        ]);

        $outbound->total_price += ($request->quantity * $item->price);
        $outbound->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Outbound record created successfully',
            'outbound' => $outbound
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:1',
        ], [
            'quantity.min' => 'Jumlah barang minimal 1'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $outboundItemTemp = OutboundItemTemp::where('id', $id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $item = Item::find($outboundItemTemp->item_id);

        if ($item->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok barang hanya tersisa ' . $item->stock
            ]);
        }

        $outboundItemTemp->update([
            'quantity' => $request->quantity,
            'price' => $item->price,
            'total_price' => $request->quantity * $item->price,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Outbound record updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $outboundItemTemp = OutboundItemTemp::where('id', $id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $outboundItemTemp->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Barang berhasil dihapus',
        ]);
    }

    public function cancelOutbound(Request $request)
    {
        $outbound = OutboundTemp::where('outbound_number', $request->outbound_number)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $outbound->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil dibatalkan',
        ]);
    }

    public function storeOutbound(Request $request)
    {
        $outboundTemp = OutboundTemp::where('user_id', Auth::user()->id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $outboundTemp->items->each(function ($outboundItem) {
            $item = Item::find($outboundItem->item_id);
            $item->stock -= $outboundItem->quantity;
            $item->save();
        });

        $outbound = Outbound::create([
            'outbound_number' => $outboundTemp->outbound_number,
            'release_to' => $outboundTemp->release_to,
            'release_reason' => $outboundTemp->release_reason,
            'request_note_number' => $outboundTemp->request_note_number,
            'delivery_note_number' => $outboundTemp->delivery_note_number,
            'date_released' => $outboundTemp->date_released,
            'received_by' => $outboundTemp->received_by,
            'released_by' => $request->released_by,
            'approved_by' => $request->approved_by,
            'total_price' => $outboundTemp->total_price,
            'branch_id' => $outboundTemp->branch_id,
            'user_id' => $outboundTemp->user_id,
        ]);

        $uploadedPhotos = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $fileName = time() . '_' . $photo->getClientOriginalName();

                $path = $photo->storeAs('assets/outbound/photos', $fileName, 'public');
                $uploadedPhotos[] = $path;
            }
        }

        $outbound->photo = json_encode($uploadedPhotos);
        $outbound->save();


        $outbound->items()->createMany(
            $outboundTemp->items->map(function ($item) use ($outbound) {
                return [
                    'outbound_id' => $outbound->id,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $item->total_price,
                    'branch_id' => $item->branch_id,
                ];
            })->toArray()
        );

        $outboundTemp->delete();

        $document = $this->saveDocument($outbound->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil dikonfirmasi',
            'outbound_id' => $outbound->id,
            // 'document' => $document['pdf']
        ]);
    }

    public function checkOutbound()
    {
        $outboundTemp = OutboundTemp::where('user_id', Auth::user()->id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        if ($outboundTemp) {
            return response()->json([
                'exist' => true
            ]);
        } else {
            return response()->json([
                'exist' => false
            ]);
        }
    }

    public function saveDocument($id, $type = 'save')
    {
        /* TYPE [SAVE, PREVIEW, DOWNLOAD]
            SAVE => GENERATE FILE UNTUK PREVIEW HALAMAN SIGN 
            PREVIEW => PREVIEW DOKUMEN DI RIWAYAT PENGELUARAN (LIHAT DOKUMEN)
            DOWNLOAD => UNTUK UNDUH DOKUMEN DI HALAMAN SIGN */

        try {
            $outbound = Outbound::find($id);

            $docxPath = storage_path('app/public/documents/outbounds/' . str_replace('/', '-', $outbound->outbound_number) . '.docx');

            // GENERATE DOCX FROM TEMPLATE
            if (!file_exists($docxPath)) {
                $regencyName = Auth::user()->branchOffice->regency->name;
                $regencyName = strtolower($regencyName);
                $regencyName = preg_replace('/^(KABUPATEN|KOTA)\s+/i', '', $regencyName);
                $regencyName = ucwords($regencyName);

                $phpWord = new TemplateProcessor('template_outbound.docx');

                // Set template values
                $phpWord->setValue('branch', strtoupper(Auth::user()->branchOffice->name));
                $phpWord->setValue('outbound_number', $outbound->outbound_number);
                $phpWord->setValue('created_at', $outbound->created_at->locale('id')->isoFormat('D MMMM YYYY'));
                $phpWord->setValue('release_to', $outbound->release_to);
                $phpWord->setValue('release_reason', $outbound->release_reason);
                $phpWord->setValue('request_note_number', $outbound->request_note_number);
                $phpWord->setValue('delivery_note_number', $outbound->delivery_note_number);
                $phpWord->setValue('regency', $regencyName);
                $phpWord->setValue('date', now()->locale('id')->isoFormat('D MMMM YYYY'));
                $phpWord->setValue('approved_by', $outbound->approvedBy->name);
                $phpWord->setValue('released_by', $outbound->releasedBy->name);
                $phpWord->setValue('received_by', $outbound->received_by);

                if ($outbound->signatures->count() >= 3) {
                    Log::info('masuk sini');
                    $signatures = [
                        'approved_by' => $outbound->signatures->where('position', 'PENGESAH')->first()->signature_path,
                        'released_by' => $outbound->signatures->where('position', 'PENANGGUNG JAWAB')->first()->signature_path,
                        'received_by' => $outbound->signatures->where('position', 'PENERIMA')->first()->signature_path,
                    ];

                    $phpWord->setImageValue('sign_approved_by', [
                        'path' => storage_path('app/public/' . $signatures['approved_by']),
                        'width' => 100,
                        'height' => 50,
                        'ratio' => false,
                    ]);
                    $phpWord->setImageValue('sign_released_by', [
                        'path' => storage_path('app/public/' . $signatures['released_by']),
                        'width' => 100,
                        'height' => 50,
                        'ratio' => false,
                    ]);
                    $phpWord->setImageValue('sign_received_by', [
                        'path' => storage_path('app/public/' . $signatures['received_by']),
                        'width' => 100,
                        'height' => 50,
                        'ratio' => false,
                    ]);
                } else {
                    $phpWord->setValue('sign_approved_by', ' ');
                    $phpWord->setValue('sign_released_by', ' ');
                    $phpWord->setValue('sign_received_by', ' ');
                }

                $photos = json_decode($outbound->photo, true);

                foreach ($photos as $index => $photo) {

                    if ($photo == null) {
                        $phpWord->setValue('photo_' . ($index + 1), ' ');
                    } else {
                        $imageInfo = getimagesize(storage_path('app/public/' . $photo));
                        $originalWidth = $imageInfo[0];
                        $originalHeight = $imageInfo[1];
                        $originalRatio = $originalWidth / $originalHeight;

                        $targetWidth = 345;
                        $targetHeight = 613;

                        // Jika rasio lebih lebar (seperti 16:9)
                        if ($originalRatio > 1) {
                            $phpWord->setImageValue('photo_' . ($index + 1), [
                                'path' => storage_path('app/public/' . $photo),
                                'width' => 345,
                                'height' => 192,
                                'ratio' => false,
                            ]);
                        } else {
                            // Untuk gambar portrait (9:16), gunakan setting normal
                            $phpWord->setImageValue('photo_' . ($index + 1), [
                                'path' => storage_path('app/public/' . $photo),
                                'width' => $targetWidth,
                                'height' => $targetHeight,
                                'ratio' => true
                            ]);
                        }
                    }
                }

                // Process items
                $values = [];
                foreach ($outbound->items as $index => $outboundItem) {
                    $total = $outboundItem->quantity * $outboundItem->price;

                    $values[$index] = [
                        'no' => $index + 1,
                        'item_name' => $outboundItem->item->name,
                        'quantity' => $outboundItem->quantity,
                        'price' => number_format($outboundItem->price, 0, ',', '.'),
                        'total' => number_format($total, 0, ',', '.')
                    ];
                }

                $phpWord->cloneRowAndSetValues('no', $values);

                // Output ke browser
                $tempDocx = storage_path('app/public/documents/outbounds/' . str_replace('/', '-', $outbound->outbound_number) . '.docx');
                $phpWord->saveAs($tempDocx);

                $outbound->update(['document_path' => 'documents/outbounds/' . str_replace('/', '-', $outbound->outbound_number) . '.docx']);
            }

            if ($type == 'preview' || $type == 'download') {
                $pdfPath = storage_path('app/public/documents/outbounds/' . str_replace('/', '-', $outbound->outbound_number) . '.pdf');

                if (file_exists($pdfPath)) {
                    $content = file_get_contents($pdfPath);

                    return response($content, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . $outbound->outbound_number . '.pdf"',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]);
                } else {
                    $inputPath = $docxPath;
                    $outputPath = $pdfPath;

                    try {
                        $this->gotenbergService->convertDocxToPdf($inputPath, $outputPath);
                    } catch (Exception $e) {
                        return response()->json([
                            'error' => $e->getMessage(),
                        ], 500);
                    }

                    // Baca file PDF
                    $content = file_get_contents($pdfPath);

                    $outbound->update(['document_path' => 'documents/outbounds/' . str_replace('/', '-', $outbound->outbound_number) . '.docx']);

                    if ($type == 'preview') {
                        unlink($docxPath);
                    }

                    return response($content, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . $outbound->outbound_number . '.pdf"',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]);
                }
            }

            return [
                'status' => 'success',
                'message' => 'DOCX berhasil dibuat',
            ];
        } catch (\Exception $e) {
            log::info($e->getMessage());
        }
    }

    public function getOutbounds()
    {
        $outbounds = OutboundItemTemp::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($outbounds)
            ->addIndexColumn()
            ->addColumn('item', function ($outbound) {
                return $outbound->item->name;
            })
            ->addColumn('price', function ($outbound) {
                return 'Rp ' . number_format($outbound->price, 0, ',', '.');
            })
            ->addColumn('subtotal', function ($outbound) {
                return 'Rp ' . number_format($outbound->quantity * $outbound->price, 0, ',', '.');
            })
            ->addColumn('action', function ($outbound) {
                $outboundJson = htmlspecialchars(json_encode($outbound), ENT_QUOTES, 'UTF-8');
                Log::info($outboundJson);
                return "
                <div class='flex items-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-item');
                            \$dispatch('set-item', {$outboundJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-item');
                            \$dispatch('set-item', {$outboundJson})
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

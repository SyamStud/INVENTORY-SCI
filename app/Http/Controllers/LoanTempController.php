<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Loan;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\LoanTemp;
use App\Models\LoanAsset;
use Illuminate\Http\Request;
use App\Models\LoanAssetTemp;
use App\Services\GotenbergService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class LoanTempController extends Controller
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
        $loan = LoanTemp::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->first();

        $assets = Asset::where('branch_id', Auth::user()->branch_id)->where('availability', 'ready')->get();
        $employees = Employee::where('branch_id', Auth::user()->branch_id)->get();

        return view('pages.main.loan', [
            'assets' => $assets,
            'employees' => $employees,
            'loan' => $loan
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_name' => 'required',
            'loan_number' => 'required',
            'asset_id' => 'required',
            // 'quantity' => 'required',
            'duration' => 'required',
            'loan_check' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $isExist = LoanTemp::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->exists();

        if (!$isExist) {
            $loan = LoanTemp::create([
                'loan_number' => $request->loan_number,
                'customer_name' => $request->customer_name,
                'branch_id' => Auth::user()->branch_id,
                'user_id' => Auth::user()->id,
            ]);
        } else {
            $loan = LoanTemp::where('branch_id', Auth::user()->branch_id)
                ->where('user_id', Auth::user()->id)
                ->first();

            $loan->update([
                'loan_number' => $request->loan_number,
                'customer_name' => $request->customer_name,
            ]);
        }

        $totalCost = 0;

        $isExist = LoanAssetTemp::where('loan_temp_id', $loan->id)
            ->where('asset_id', $request->asset_id)
            ->where('branch_id', Auth::user()->branch_id)
            ->exists();

        if ($isExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang sudah ada'
            ]);
        }

        $loan->assets()->create([
            'loan_temp_id' => $loan->id,
            'asset_id' => $request->asset_id,
            'duration' => $request->duration,
            'notes' => $request->notes ?? null,
            'loan_check' => $request->loan_check,
            'branch_id' => Auth::user()->branch_id,
        ]);

        $asset = Asset::find($request->asset_id);
        $asset->update(['availability' => 'on_loan']);

        return response()->json([
            'status' => 'success',
            'message' => 'Loan record created successfully',
            'loan' => $loan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loanAssetLoanAsset = LoanAssetTemp::where('id', $id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $validation = Validator::make($request->all(), [
            'loan_asset_id' => 'required',
            // 'quantity' => 'required',
            'duration' => 'required',
            'loan_check' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first()
            ]);
        }

        $loanAssetLoanAsset->update([
            // 'quantity' => $request->quantity,
            'duration' => $request->duration,
            'loan_check' => $request->loan_check,
        ]);

        if ($request->has('notes')) {
            $loanAssetLoanAsset->update(['notes' => $request->notes]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Loan record updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loanAsset = LoanAssetTemp::where('id', $id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $asset = Asset::with('brand')->find($loanAsset->asset_id);

        $assetData = [
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'brand_name' => $asset->brand->name,
            'serial_number' => $asset->serial_number
        ];

        $asset->update(['availability' => 'ready']);

        $loanAsset->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Barang berhasil dihapus',
            'loan_asset' => $assetData
        ]);
    }

    public function cancelLoan(Request $request)
    {
        $loan = LoanTemp::where('loan_number', $request->loan_number)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $loan->assets->each(function ($asset) {
            $asset->asset->update(['availability' => 'ready']);
        });

        $assetsData = $loan->assets->map(function ($asset) {
            return [
                'asset_id' => $asset->asset->id,
                'asset_name' => $asset->asset->name,
                'brand_name' => $asset->asset->brand->name,
                'serial_number' => $asset->asset->serial_number
            ];
        });

        $loan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Penerimaan berhasil dibatalkan',
            'assets' => $assetsData
        ]);
    }

    public function storeLoan(Request $request)
    {
        $loanTemp = LoanTemp::where('user_id', Auth::user()->id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $loan = Loan::create([
            'loan_number' => $loanTemp->loan_number,
            'customer_name' => $loanTemp->customer_name,
            'operation_head' => $request->operation_head,
            'general_division' => $request->general_division,
            'loan_officer' => $request->loan_officer,
            'branch_id' => Auth::user()->branch_id,
            'user_id' => Auth::user()->id,
        ]);

        $uploadedPhotos = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $fileName = time() . '_' . $photo->getClientOriginalName();

                $path = $photo->storeAs('assets/photos', $fileName, 'public');
                $uploadedPhotos[] = $path;
            }
        }

        $loan->photos = json_encode($uploadedPhotos);
        $loan->save();

        $loan->assets()->createMany(
            $loanTemp->assets->map(function ($asset) use ($loan) {
                return [
                    'loan_id' => $loan->id,
                    'asset_id' => $asset->asset_id,
                    'quantity' => $asset->quantity,
                    'duration' => $asset->duration,
                    'loan_check' => $asset->loan_check,
                    'notes' => $asset->notes,
                    'branch_id' => $asset->branch_id,
                ];
            })->toArray()
        );

        $loanTemp->delete();

        $document = $this->saveDocument($loan->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Penerimaan berhasil dikonfirmasi',
            'loan_id' => $loan->id,
        ]);
    }

    public function checkLoan()
    {
        $loan = LoanTemp::where('user_id', Auth::user()->id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        if ($loan) {
            return response()->json([
                'exist' => true
            ]);
        } else {
            return response()->json([
                'exist' => false
            ]);
        }
    }

    public function search(Request $request)
    {
        $loans = Loan::where('branch_id', Auth::user()->branch_id)
            ->where('loan_number', $request->loan_number)
            ->where('status', 'on_loan')
            ->with('assets.asset.brand', 'assets.asset', 'operationHead', 'generalDivision', 'loanOfficer')
            ->first();

        if (!$loans) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'loan' => $loans
        ]);
    }

    public function receipt($id)
    {
        try {
            $loan = Loan::find($id);

            $phpWord = new TemplateProcessor('template_loan.docx');

            // Set template values
            $phpWord->setValue('customer_name', $loan->customer_name);
            $phpWord->setValue('loan_number', $loan->loan_number);
            $phpWord->setValue('operation_head', $loan->operationHead->name);
            $phpWord->setValue('npp_head', $loan->operationHead->npp);
            $phpWord->setValue('general_division', $loan->generalDivision->name);
            $phpWord->setValue('npp_general', $loan->generalDivision->npp);
            $phpWord->setValue('loan_officer', $loan->loanOfficer->name);
            $phpWord->setValue('npp_officer', $loan->loanOfficer->npp);

            $photos = json_decode($loan->photos, true);

            foreach ($photos as $index => $photo) {
                // Get image dimensions
                $imageInfo = getimagesize(storage_path('app/public/' . $photo));
                $originalWidth = $imageInfo[0];
                $originalHeight = $imageInfo[1];
                $originalRatio = $originalWidth / $originalHeight;
                Log::info($originalRatio);

                // Target width tetap 345
                $targetWidth = 345;
                $targetHeight = 613;

                // Kalkulasi height yang sebenarnya setelah scaling
                $actualHeight = $targetWidth / $originalRatio;

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

            // Process assets
            $values = [];
            foreach ($loan->assets as $index => $loanAsset) {
                $values[$index] = [
                    'no' => $index + 1,
                    'tag_number' => $loanAsset->asset->tag_number,
                    'asset_name' => $loanAsset->asset->name,
                    'brand' => $loanAsset->asset->brand->name,
                    'serial_number' => $loanAsset->asset->serial_number,
                    'quantity' => $loanAsset->quantity,
                    'duration' => $loanAsset->duration,
                    'notes' => $loanAsset->notes,
                    'loan_check' => $loanAsset->loan_check,
                    'return_check' => $loanAsset->return_check ?? '-',
                ];
            }

            $phpWord->cloneRowAndSetValues('no', $values);

            // Output ke browser
            $tempDocx = storage_path('app/public/loan/loan.docx');
            $phpWord->saveAs($tempDocx);

            $docxPath = storage_path('app/public/loan/loan.docx');
            $pdfPath = storage_path('app/public/loan/' . $loan->loan_number . '.pdf');

            $pythonScriptPath = base_path('scripts/word2pdf.py');

            // Jalankan perintah untuk mengonversi DOCX ke PDF menggunakan Python
            $command = "python $pythonScriptPath $docxPath $pdfPath";
            exec($command);

            // Baca file PDF
            $content = file_get_contents($pdfPath);

            // Cleanup temp files
            unlink($tempDocx);
            // unlink($pdfPath);

            // return response($content, 200, [
            //     'Content-Type' => 'application/pdf',
            //     'Content-Disposition' => 'inline; filename="' . $loan->loan_number . '.pdf"',
            //     'Cache-Control' => 'no-cache, no-store, must-revalidate',
            //     'Pragma' => 'no-cache',
            //     'Expires' => '0',
            // ]);

            return response()->json([
                'status' => 'success',
                'message' => 'PDF berhasil dibuat',
                'pdf' => $pdfPath,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function saveDocument($id, $type = 'save')
    {
        /* TYPE [SAVE, PREVIEW, DOWNLOAD]
            SAVE => GENERATE FILE UNTUK PREVIEW HALAMAN SIGN 
            PREVIEW => PREVIEW DOKUMEN DI RIWAYAT PENGELUARAN (LIHAT DOKUMEN)
            DOWNLOAD => UNTUK UNDUH DOKUMEN DI HALAMAN SIGN */

        try {
            $loan = Loan::find($id);

            $docxPath = storage_path('app/public/documents/loans/' . str_replace('/', '-', $loan->loan_number) . '.docx');
            $pdfPath = storage_path('app/public/documents/loans/' . str_replace('/', '-', $loan->loan_number) . '.pdf');

            // GENERATE DOCX FROM TEMPLATE AND CONVERT TO PDF
            if ($type == 'save' || ($type == 'preview' && !file_exists($pdfPath)) || ($type == 'download' && !file_exists($pdfPath))) {
                $phpWord = new TemplateProcessor('template_loan.docx');

                // Set template values
                $phpWord->setValue('customer_name', $loan->customer_name);
                $phpWord->setValue('loan_number', $loan->loan_number);
                $phpWord->setValue('operation_head', $loan->operationHead->name);
                $phpWord->setValue('npp_head', $loan->operationHead->npp);
                $phpWord->setValue('general_division', $loan->generalDivision->name);
                $phpWord->setValue('npp_general', $loan->generalDivision->npp);
                $phpWord->setValue('loan_officer', $loan->loanOfficer->name);
                $phpWord->setValue('npp_officer', $loan->loanOfficer->npp);

                if ($loan->signatures->count() >= 3) {
                    $signatures = [
                        'operation_head' => $loan->signatures->where('position', 'KEPALA BIDANG OPERASI')->first()->signature_path,
                        'general_division' => $loan->signatures->where('position', 'BAGIAN UMUM')->first()->signature_path,
                        'loan_officer' => $loan->signatures->where('position', 'PETUGAS PINJAMAN')->first()->signature_path,
                    ];

                    $phpWord->setImageValue('sign_operation_head', [
                        'path' => storage_path('app/public/' . $signatures['operation_head']),
                        'width' => 100,
                        'height' => 50,
                        'ratio' => false,
                    ]);
                    $phpWord->setImageValue('sign_general_division', [
                        'path' => storage_path('app/public/' . $signatures['general_division']),
                        'width' => 100,
                        'height' => 50,
                        'ratio' => false,
                    ]);
                    $phpWord->setImageValue('sign_loan_officer', [
                        'path' => storage_path('app/public/' . $signatures['loan_officer']),
                        'width' => 100,
                        'height' => 50,
                        'ratio' => false,
                    ]);
                }

                $photos = json_decode($loan->photos, true);

                foreach ($photos as $index => $photo) {
                    // Get image dimensions
                    $imageInfo = getimagesize(storage_path('app/public/' . $photo));
                    $originalWidth = $imageInfo[0];
                    $originalHeight = $imageInfo[1];
                    $originalRatio = $originalWidth / $originalHeight;

                    // Target width tetap 345
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

                // Process assets
                $values = [];
                foreach ($loan->assets as $index => $loanAsset) {
                    $values[$index] = [
                        'no' => $index + 1,
                        'tag_number' => $loanAsset->asset->tag_number,
                        'asset_name' => $loanAsset->asset->name,
                        'brand' => $loanAsset->asset->brand->name,
                        'serial_number' => $loanAsset->asset->serial_number,
                        'duration' => $loanAsset->duration,
                        'notes' => $loanAsset->notes,
                        'loan_check' => $loanAsset->loan_check,
                        'return_check' => $loanAsset->return_check ?? '-',
                    ];
                }

                $phpWord->cloneRowAndSetValues('no', $values);

                // Output ke browser
                $tempDocx = storage_path('app/public/documents/loans/' . str_replace('/', '-', $loan->loan_number) . '.docx');
                $phpWord->saveAs($tempDocx);

                $inputPath = $docxPath;
                $outputPath = $pdfPath;

                try {
                    $this->gotenbergService->convertDocxToPdf($inputPath, $outputPath);
                } catch (Exception $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                    ], 500);
                }

                $loan->update(['document_path' => 'documents/loans/' . str_replace('/', '-', $loan->loan_number) . '.pdf']);

                unlink($docxPath);
            }

            if ($type == 'preview' || $type == 'download') {
                $content = file_get_contents($pdfPath);

                return response($content, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $loan->loan_number . '.pdf"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
            }

            return [
                'status' => 'success',
                'message' => 'DOCX berhasil dibuat',
            ];
        } catch (\Exception $e) {
            log::info($e->getMessage());
        }
    }

    public function getLoans()
    {
        $loans = LoanAssetTemp::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('tag_number', function ($loan) {
                return $loan->asset->tag_number;
            })
            ->addColumn('asset', function ($loan) {
                return $loan->asset->name;
            })
            ->addColumn('brand', function ($loan) {
                return $loan->asset->brand->name;
            })
            ->addColumn('serial_number', function ($loan) {
                return $loan->asset->serial_number;
            })
            ->addColumn('duration', function ($loan) {
                return $loan->duration . ' Hari';
            })
            ->addColumn('action', function ($loan) {
                $loanJson = htmlspecialchars(json_encode($loan), ENT_QUOTES, 'UTF-8');
                Log::info($loanJson);
                return "
                <div class='flex assets-center gap-2'>
                    <button style='background-color: #C07F00;' class='flex assets-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' 
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'edit-asset');
                            \$dispatch('set-asset', {$loanJson})
                        \">
                        <img class='w-4' src='https://img.icons8.com/?size=100&id=NiI6TTAAFkQH&format=png&color=FFFFFF' alt=''>
                        Ubah
                    </button>

                    <button style='background-color: #C62E2E;' class='flex assets-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium'
                        x-data='' 
                        x-on:click.prevent=\"
                            \$dispatch('open-modal', 'delete-asset');
                            \$dispatch('set-asset', {$loanJson})
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

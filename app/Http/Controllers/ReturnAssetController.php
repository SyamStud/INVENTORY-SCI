<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanAsset;
use App\Models\ReturnAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Validator;

class ReturnAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.main.return');
    }

    public function storeReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assets' => 'required|array',
            'assets.*.loan_asset_id' => 'required|exists:loan_assets,id',
            'assets.*.return_check' => 'required|in:baik,rusak',
            'assets.*.notes' => 'nullable|string'
        ], [
            'assets.required' => 'Kondisi pengembalian tidak boleh kosong',
            'assets.*.return_check.required' => 'Kondisi pengembalian tidak boleh kosong',
            'assets.*.return_check.in' => 'Kondisi pengembalian tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        foreach ($request->assets as $asset) {
            $loanAsset = LoanAsset::find($asset['loan_asset_id']);

            if (!$asset['return_check']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kondisi pengembalian tidak boleh kosong'
                ]);
            }

            $loanAsset->update([
                'return_check' => $asset['return_check'],
                'notes' => $asset['notes']
            ]);
        }

        $loanAsset = LoanAsset::find($request->assets[0]['loan_asset_id']);
        $loanAsset->loan->update([
            'status' => 'returned'
        ]);

        $this->saveDocument($loanAsset->loan->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'loan_id' => $loanAsset->loan->id
        ]);
    }

    public function saveDocument($id, $preview = false)
    {
        try {
            $loan = Loan::find($id);

            $docxPath = storage_path('app/public/documents/loans/' . str_replace('/', '-', $loan->loan_number) . '-RETURNED.docx');
            $pdfPath = storage_path('app/public/documents/loans/' . str_replace('/', '-', $loan->loan_number) . '-RETURNED.pdf');

            $unReturnedPdfPath = storage_path('app/public/documents/loans/' . str_replace('/', '-', $loan->loan_number) . '.pdf');

            if (file_exists($unReturnedPdfPath)) {
                unlink($unReturnedPdfPath);
            }

            if (file_exists($pdfPath)) {
                $content = file_get_contents($pdfPath);

                if ($preview) {
                    return response($content, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . $loan->loan_number . '.pdf"',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]);
                } else {
                    return [
                        'status' => 'success',
                        'message' => 'PDF berhasil dibuat',
                        'pdf' => str_replace(storage_path('app/public/'), '', $pdfPath),
                    ];
                }
            }

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
                    'quantity' => $loanAsset->quantity,
                    'duration' => $loanAsset->duration,
                    'notes' => $loanAsset->notes,
                    'loan_check' => $loanAsset->loan_check,
                    'return_check' => $loanAsset->return_check ?? '-',
                ];
            }

            $phpWord->cloneRowAndSetValues('no', $values);

            // Output ke browser
            $tempDocx = storage_path('app/public/documents/loans/' . str_replace('/', '-', $loan->loan_number) . '-RETURNED.docx');
            $phpWord->saveAs($tempDocx);

            $pythonScriptPath = base_path('scripts/word2pdf.py');

            // Jalankan perintah untuk mengonversi DOCX ke PDF menggunakan Python
            $command = "python $pythonScriptPath $docxPath $pdfPath";
            exec($command);

            // Baca file PDF
            $content = file_get_contents($pdfPath);

            // Cleanup temp files
            unlink($tempDocx);

            $loan->update(['document_path' => 'documents/loans/' . str_replace('/', '-', $loan->loan_number) . '-RETURNED.pdf']);

            if ($preview) {
                return response($content, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $loan->loan_number . '.pdf"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
            } else {
                return [
                    'status' => 'success',
                    'message' => 'PDF berhasil dibuat',
                    'pdf' => str_replace(storage_path('app/public/'), '', $pdfPath),
                ];
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}

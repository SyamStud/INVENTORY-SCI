<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\Loan;
use App\Models\Outbound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;

class DocumentSigningController extends Controller
{
    public function index()
    {
        return view('pages.employee.approvalDocuments');
    }

    public function loanIndex()
    {
        return view('pages.employee.loanDocuments');
    }

    public function outboundIndex()
    {
        return view('pages.employee.outboundDocuments');
    }

    public function signIndex()
    {
        return view('pages.employee.sign');
    }

    public function signLoan($id)
    {
        $loan = Loan::find($id);

        return view('pages.employee.signLoan', compact('loan'));
    }

    public function storeSignature(Request $request, $id)
    {
        try {
            $request->validate([
                'signature' => 'required|string'
            ]);

            $document = null;

            if ($request->type == 'loan') {
                $document = Loan::find($id);
            } else if ($request->type == 'outbound') {
                $document = Outbound::find($id);
            } else {
                $document = Inbound::find($id);
            }

            $signature = $request->input('signature');
            $signature = str_replace('data:image/png;base64,', '', $signature);
            $signature = str_replace(' ', '+', $signature);

            $signatureImage = base64_decode($signature);

            $number = $document->loan_number ?? $document->outbound_number ?? $document->inbound_number;

            $filenameBase = 'signatures/' . $request->type . '/' . str_replace('/', '-', $number) . '/';

            // Tentukan posisi yang sudah ditandatangani
            $signedPositions = $document->signatures->pluck('position')->toArray();

            // Posisi yang mungkin sesuai
            $positions = [];

            if ($document instanceof Loan) {
                if ($document->operation_head == Auth::user()->employee_id) {
                    $positions['KEPALA BIDANG OPERASI'] = 'KEPALA BIDANG OPERASI';
                }
                if ($document->loan_officer == Auth::user()->employee_id) {
                    $positions['PETUGAS PINJAMAN'] = 'PETUGAS PINJAMAN';
                }
                if ($document->general_division == Auth::user()->employee_id) {
                    $positions['BAGIAN UMUM'] = 'BAGIAN UMUM';
                }
            }

            // Filter posisi yang belum ditandatangani
            $positionsToSign = array_diff_key($positions, array_flip($signedPositions));

            if (empty($positionsToSign)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua posisi Anda sudah menandatangani dokumen ini.',
                ], 403);
            }

            // Buat tanda tangan untuk setiap posisi yang belum ditandatangani
            foreach ($positionsToSign as $key => $position) {
                $filename = $filenameBase . $position . '.png';

                Storage::disk('public')->put($filename, $signatureImage);

                $document->signatures()->create([
                    'position' => $position,
                    'signature_path' => $filename,
                    'is_signed' => true,
                    'signed_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Signatures saved successfully for all positions.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving signature: ' . $e->getMessage()
            ], 500);
        }
    }

    public function LoanPreview($id)
    {
        try {
            $loan = Loan::find($id);

            $path = storage_path('app/public/' . $loan->document_path);

            $content = file_get_contents($path);

            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $loan->loan_number . '.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPendingLoans()
    {
        $loans = Loan::where('status', 'pending')
            ->where('branch_id', Auth::user()->branch_id)
            ->Where(function ($query) {
                $query->where('loan_officer', Auth::user()->employee_id)
                    ->whereDoesntHave('signatures', function ($q) {
                        $q->where('position', 'PETUGAS PINJAMAN');
                    });
            })
            ->orWhere(function ($query) {
                $query->where('operation_head', Auth::user()->employee_id)
                    ->whereDoesntHave('signatures', function ($q) {
                        $q->where('position', 'KEPALA BIDANG OPERASI');
                    });
            })
            ->orWhere(function ($query) {
                $query->where('general_division', Auth::user()->employee_id)
                    ->whereDoesntHave('signatures', function ($q) {
                        $q->where('position', 'BAGIAN UMUM');
                    });
            })
            ->get();

        return DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('action', function ($loan) {
                return "
                <div class=''>
                    <a style='background-color: #133E87;' class='flex w-max items-center gap-2 px-3 py-1 text-white rounded-md text-sm font-medium' href='/documents/loans/sign/" . $loan->id . "'>
                        <img class='w-5' src='https://img.icons8.com/?size=100&id=9ZFMqzgXThYz&format=png&color=FFFFFF' alt=''>
                        Tanda Tangan
                    </a>
                </div>";
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}

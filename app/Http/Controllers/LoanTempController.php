<?php

namespace App\Http\Controllers;

use App\Models\LoanTemp;
use App\Models\Loan;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\LoanAsset;
use App\Models\LoanAssetTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class LoanTempController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loan = LoanTemp::where('branch_id', Auth::user()->branch_id)
            ->where('user_id', Auth::user()->id)
            ->first();

        $assets = Asset::where('branch_id', Auth::user()->branch_id)->get();
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
            'quantity' => 'required',
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
            'quantity' => $request->quantity,
            'duration' => $request->duration,
            'notes' => $request->notes ?? null,
            'loan_check' => $request->loan_check,
            'branch_id' => Auth::user()->branch_id,
        ]);

        $totalCost += $request->quantity * $request->cost;

        $loan->update(['total_cost' => $totalCost]);

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
            'quantity' => 'required',
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
            'quantity' => $request->quantity,
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
        $loanAssetLoanAsset = LoanAssetTemp::where('id', $id)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $loanAssetLoanAsset->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Barang berhasil dihapus',
        ]);
    }

    public function cancelLoan(Request $request)
    {
        $loan = LoanTemp::where('loan_number', $request->loan_number)
            ->where('branch_id', Auth::user()->branch_id)
            ->first();

        $loan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Penerimaan berhasil dibatalkan',
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

            // Process assets
            $values = [];
            foreach ($loan->assets as $index => $loanAsset) {
                $values[$index] = [
                    'no' => $index + 1,
                    'asset_name' => $loanAsset->asset->name,
                    'quantity' => $loanAsset->quantity,
                    'duration' => $loanAsset->duration,
                    'notes' => $loanAsset->notes,
                    'loan_check' => $loanAsset->loan_check,
                    'return_check' => '-',
                ];
            }

            $phpWord->cloneRowAndSetValues('no', $values);

            // Output ke browser
            $tempDocx = storage_path('app/public/loan/loan.docx');
            $phpWord->saveAs($tempDocx);

            $docxPath = storage_path('app/public/loan/loan.docx');
            $pdfPath = storage_path('app/public/loan/loan.pdf');

            $pythonScriptPath = base_path('scripts/word2pdf.py');

            // Jalankan perintah untuk mengonversi DOCX ke PDF menggunakan Python
            $command = "python $pythonScriptPath $docxPath $pdfPath";
            exec($command);

            // Baca file PDF
            $content = file_get_contents($pdfPath);

            // Cleanup temp files
            unlink($tempDocx);
            unlink($pdfPath);

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

    public function getLoans()
    {
        $loans = LoanAssetTemp::where('branch_id', Auth::user()->branch_id)->orderByDesc('created_at');

        return DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('asset', function ($loan) {
                return $loan->asset->name;
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

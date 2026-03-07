<?php

namespace App\Http\Controllers;

use App\Models\BankMutation;
use App\Services\Finance\MutationImportService;
use App\Exports\BankMutationTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MutationImportController extends Controller
{
    protected MutationImportService $importService;

    public function __construct(MutationImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show import page with list of recent mutations.
     */
    public function index(Request $request)
    {
        $query = BankMutation::orderByDesc('transaction_date')->orderByDesc('created_at');

        // Filter by bank
        if ($request->filled('bank')) {
            $query->where('bank_code', $request->bank);
        }

        // Filter by used status
        if ($request->filled('status')) {
            if ($request->status === 'used') {
                $query->where('used', true);
            } elseif ($request->status === 'unused') {
                $query->where('used', false);
            }
        }

        // Search by description or invoice_number
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhere('invoice_number', 'like', "%{$searchTerm}%");
            });
        }

        $mutations = $query->paginate(25)->withQueryString();

        // Get distinct bank codes for filter dropdown
        $banks = BankMutation::select('bank_code')->distinct()->whereNotNull('bank_code')->where('bank_code', '!=', '')->pluck('bank_code');

        return view('finance.mutations.import', compact('mutations', 'banks'));
    }

    /**
     * Import mutations from uploaded Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Pilih file Excel/CSV untuk diupload.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            $result = $this->importService->importMutation($request->file('file'));

            $message = $result['imported'] . ' data mutasi berhasil diimport.';
            if ($result['skipped'] > 0) {
                $message .= ' (' . $result['skipped'] . ' baris dilewati karena data tidak lengkap)';
            }

            return redirect()
                ->route('finance.mutations.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel template for bank mutations.
     */
    public function downloadTemplate()
    {
        return Excel::download(new BankMutationTemplateExport(), 'Template_Mutasi_Bank.xlsx');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CsLead;
use App\Models\User;
use App\Helpers\PhoneHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CsGreetingController extends Controller
{
    public function index(Request $request)
    {
        $query = CsLead::where('status', CsLead::STATUS_GREETING)->with('cs');
        
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'owner') {
            $query->where(function($q) use ($user) {
                $q->where('cs_id', $user->id)
                  ->orWhereNull('cs_id');
            });
        }

        // Filter by Search (Phone)
        if ($request->filled('search')) {
            $query->where('customer_phone', 'like', '%' . $request->search . '%');
        }

        // Filter by PIC
        if ($request->filled('cs_id')) {
            $query->where('cs_id', $request->cs_id);
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('first_contact_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('first_contact_at', '<=', $request->end_date);
        }

        $greetings = $query->orderBy('first_contact_at', 'desc')->get();
        $csUsers = User::where('role', 'cs')->get();

        return view('admin.cs.greeting.index', compact('greetings', 'csUsers'));
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Set Headers (Matching image provided by user)
        $sheet->setCellValue('A1', 'TANGGAL CHAT MASUK');
        $sheet->setCellValue('B1', 'NOMOR CUSTOMER');
        $sheet->setCellValue('C1', 'PIC HANDLE');

        // Style the header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'],
            ],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);

        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin', 'owner']);

        if ($isAdmin) {
            // 2. Prepare PIC List for Data Validation (Strictly role 'cs')
            $csUsers = User::where('role', 'cs')
                ->pluck('name')
                ->toArray();
            
            $picList = '"' . implode(',', $csUsers) . '"';

            // 3. Apply Data Validation to PIC column (C2:C100)
            for ($i = 2; $i <= 100; $i++) {
                $validation = $sheet->getCell("C{$i}")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input PIC Error');
                $validation->setError('PIC tidak dikenal. Kosongkan jika ingin masuk ke Kolam Umum (Greeting).');
                $validation->setPromptTitle('Pilih PIC');
                $validation->setPrompt('Pilih nama CS untuk Langsung Konsultasi, atau kosongkan untuk Kolam Umum.');
                $validation->setFormula1($picList);
            }
        } else if ($user->role === 'cs') {
            // 2. CS User: Sheet is protected but columns are editable
            $sheet->getProtection()->setPassword('');
            $sheet->getProtection()->setSheet(true);
            $sheet->getProtection()->setSort(true);
            $sheet->getProtection()->setInsertRows(true);
            $sheet->getProtection()->setFormatCells(false);

            // Unlock Column A, B and C (Date, Phone, PIC)
            // Column C is unlocked so they can fill it for Direct Konsultasi or leave it empty for Greeting Pool
            $sheet->getStyle('A2:C1000')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        }

        // Add dummy data for example (Only Date and Phone, PIC left empty for Greeting Pool)
        $sheet->setCellValue('A2', now()->format('d M Y'));
        $sheet->setCellValue('B2', '081214696299');

        $writer = new Xlsx($spreadsheet);
        $userSuffix = str_replace(' ', '_', $user->name);
        $fileName = 'Template_Import_Greeting_' . $userSuffix . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        $file = $request->file('file');
        $rows = Excel::toArray(new class {}, $file)[0];

        // Skip header
        array_shift($rows);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Row mapping: 0=Date, 1=Phone, 2=PIC Name
                $chatDateStr = $row[0] ?? null;
                $phoneRaw = $row[1] ?? null;
                $picName = isset($row[2]) ? trim($row[2]) : '';

                if (empty($phoneRaw)) {
                    continue; 
                }

                // 1. Normalize Phone (custom 812... format)
                $phoneNormalized = PhoneHelper::normalizeForGreeting($phoneRaw);

                // 2. Find PIC User (Verify they have CS role)
                $picUser = User::where('name', 'like', $picName)
                    ->where('role', 'cs')
                    ->first();

                $status = CsLead::STATUS_GREETING;
                $assignedCsId = null;

                if ($picUser) {
                    $status = CsLead::STATUS_KONSULTASI;
                    $assignedCsId = $picUser->id;
                }

                // 3. Parse Date
                try {
                    // PhpSpreadsheet often returns date as serial number or formatted string
                    if (is_numeric($chatDateStr)) {
                        $chatDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($chatDateStr));
                    } else {
                        $chatDate = Carbon::parse($chatDateStr);
                    }
                } catch (\Exception $e) {
                    $chatDate = now();
                }

                // 4. Create Greeting Lead (Isolated from Customer table)
                $lead = CsLead::create([
                    'customer_name' => 'Guest ' . substr($phoneNormalized, -4),
                    'customer_phone' => $phoneNormalized,
                    'status' => $status,
                    'cs_id' => $assignedCsId,
                    'first_contact_at' => $chatDate,
                    'last_activity_at' => $chatDate,
                    'source' => CsLead::SOURCE_WHATSAPP,
                ]);

                // 5. Log Activity if assigned
                // 5. Log Activity
                $lead->activities()->create([
                    'user_id' => $assignedCsId,
                    'type' => 'CHAT',
                    'content' => $assignedCsId ? 'Lead diimport dan langsung dikelola oleh ' . $picUser->name : 'Lead diimport ke kolam umum (Greeting).',
                    'created_at' => $chatDate,
                ]);

                $successCount++;
            }

            DB::commit();
            
            $message = "Berhasil mengimpor {$successCount} data.";
            if ($errorCount > 0) {
                $message .= " Gagal {$errorCount} data.";
            }

            return back()->with('success', $message)->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[CS Greeting Import] Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.'], 400);
        }

        try {
            CsLead::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => count($ids) . ' data greeting berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data.'], 500);
        }
    }

    public function claim(CsLead $lead)
    {
        Log::debug("[CS Greeting Claim] Attempting to claim lead ID: " . $lead->id . " by User ID: " . Auth::id());

        if ($lead->cs_id !== null) {
            Log::debug("[CS Greeting Claim] Lead already assigned to: " . $lead->cs_id);
            return response()->json(['success' => false, 'message' => 'Lead sudah diambil oleh ' . ($lead->cs->name ?? 'staf lain')], 400);
        }

        $user = Auth::user();
        if ($user->role !== 'cs' && $user->role !== 'admin' && $user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Hanya staf CS yang dapat mengambil lead.'], 403);
        }

        DB::beginTransaction();
        try {
            // Re-fetch with row lock to prevent race conditions
            $lockedLead = CsLead::where('id', $lead->id)->lockForUpdate()->first();

            if ($lockedLead->cs_id !== null) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Lead sudah baru saja diambil oleh orang lain.'], 400);
            }

            $lockedLead->update([
                'cs_id' => $user->id,
                'status' => CsLead::STATUS_KONSULTASI, // Auto upgrade to Konsultasi after claim
                'last_activity_at' => now()
            ]);

            $lockedLead->activities()->create([
                'user_id' => $user->id,
                'type' => 'CHAT',
                'content' => 'Lead berhasil di-claim dari kolam umum oleh ' . $user->name,
            ]);

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Lead berhasil diambil! Halaman akan beralih ke Konsultasi.',
                'redirect' => route('cs.dashboard')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[CS Greeting Claim] Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil lead.'], 500);
        }
    }

    public function bulkDeleteFiltered(Request $request)
    {
        try {
            $query = CsLead::where('status', CsLead::STATUS_GREETING);
            
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->role !== 'owner') {
                $query->where(function($q) use ($user) {
                    $q->where('cs_id', $user->id)
                      ->orWhereNull('cs_id');
                });
            }

            if ($request->filled('search')) {
                $query->where('customer_phone', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('cs_id')) {
                $query->where('cs_id', $request->cs_id);
            }
            if ($request->filled('start_date')) {
                $query->whereDate('first_contact_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('first_contact_at', '<=', $request->end_date);
            }

            $count = $query->count();
            
            if ($count === 0) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data yang sesuai filter untuk dihapus.'], 400);
            }

            $query->delete();

            return response()->json([
                'success' => true, 
                'message' => $count . ' data greeting hasil filter berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error("[CS Greeting Bulk Delete Filtered] Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data hasil filter.'], 500);
        }
    }
}

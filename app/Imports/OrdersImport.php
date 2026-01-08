<?php

namespace App\Imports;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrdersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Flexible key check helper
        $get = fn($keys, $default = null) => collect((array)$keys)->map(fn($k) => $row[$k] ?? null)->filter()->first() ?? $default;

        // Try to find SPK from Excel. If not found, generate one.
        $excelSpk = $get(['spk', 'no_spk', 'spk_number', 'nomor_spk', 'no_order', 'order_id', 'id_transaksi']);
        $spk = $excelSpk ?? 'SPK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        
        return new WorkOrder([
            'spk_number'    => $spk,
            'customer_name' => $get(['nama', 'nama_customer', 'customer', 'customer_name'], 'No Name'), 
            'customer_phone'=> $get(['nomor_wa', 'no_wa', 'telepon', 'phone', 'hp', 'whatsapp']) ?? '-', 
            'customer_address'=> $get(['alamat', 'address', 'lokasi']) ?? null,
            
            'shoe_brand'    => $get(['brand', 'merk', 'sepatu', 'shoe_brand']) ?? 'Unknown',
            'shoe_size'     => $get(['size', 'ukuran', 'shoe_size']) ?? '-',
            'shoe_color'    => $get(['warna', 'color', 'colour', 'shoe_color']) ?? '-',
            
            'status'        => WorkOrderStatus::DITERIMA->value,
            // 'location' field is not in model, user 'current_location' if needed or rely on WorkflowService logic
            'current_location' => 'Gudang Penerimaan', 
            'entry_date'    => ($val = $get(['tanggal_masuk', 'tanggal_masuk_workshop', 'entry_date'])) 
                                ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)) 
                                : now(),
            'estimation_date' => ($val = $get(['estimasi_selesai', 'estimasi', 'estimation'])) 
                                ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)) 
                                : now()->addDays(3),
            'priority'      => $get(['prioritas', 'priority']) ?? 'Normal',
            'created_by'    => Auth::id(),
        ]);
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class OrdersTemplateExport implements WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            ['SPK', 'Customer', 'No WA', 'Email', 'Alamat', 'Brand', 'Jenis', 'Size', 'Warna', 'Tanggal Masuk', 'Estimasi Selesai', 'Prioritas', 'Catatan'], // Header
            ['CONTOH-001', 'Budi Santoso', '08123456789', 'budi@email.com', 'Jl. Merdeka No. 1', 'Nike', 'Sneakers', '42', 'Hitam', date('Y-m-d'), date('Y-m-d', strtotime('+3 days')), 'Reguler', 'Lem outsole lepas, minta repaint putih'] // Example Data
        ];
    }

    public function title(): string
    {
        return 'Template Import Order';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF14B8A6']]], // Teal Header
            2 => ['font' => ['italic' => true, 'color' => ['argb' => 'FF6B7280']]], // Example row gray
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Priority Dropdown (Column K, starting row 3 to 1000)
                $validation = $event->sheet->getCell('K3')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih prioritas dari list.');
                $validation->setFormula1('"Reguler,Prioritas"');

                // Clone validation to rows 3-1000
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("K$i")->setDataValidation(clone $validation);
                }
            },
        ];
    }
}

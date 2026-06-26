<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MaterialTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return [
            ['Vibram XS Grip', 'Material Sol', 'PRODUCTION', 'Vibram', '40', '10', 'pcs', '350000', '5', 'Ready', 'admin@example.com'],
            ['Kulit Sapi Premium', 'Material Upper', 'SHOPPING', '', '', '5', 'lembar', '1500000', '2', 'Belanja', 'pic@example.com'],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'type',
            'category',
            'sub_category',
            'size',
            'stock',
            'unit',
            'price',
            'min_stock',
            'status',
            'pic',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Style for Required Headers
                $requiredStyle = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D1FAE5'], // Emerald 100
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '065F46'], // Emerald 800
                    ]
                ];
                
                // Style for Optional Headers
                $optionalStyle = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6'], // Gray 100
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '374151'], // Gray 700
                    ]
                ];
                
                // Apply Required Headers: name (A), type (B), stock (F), unit (G), price (H), min_stock (I), status (J)
                foreach (['A1', 'B1', 'F1', 'G1', 'H1', 'I1', 'J1'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray($requiredStyle);
                }
                
                // Apply Optional Headers: category (C), sub_category (D), size (E), pic (K)
                foreach (['C1', 'D1', 'E1', 'K1'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray($optionalStyle);
                }
                
                // Set borders to headers
                $sheet->getStyle('A1:K1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Data validation for Type (Column B)
                $typeValidation = new DataValidation();
                $typeValidation->setType(DataValidation::TYPE_LIST);
                $typeValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $typeValidation->setAllowBlank(false);
                $typeValidation->setShowDropDown(true);
                $typeValidation->setFormula1('"Material Sol,Material Upper"');
                
                // Data validation for Category (Column C)
                $categoryValidation = new DataValidation();
                $categoryValidation->setType(DataValidation::TYPE_LIST);
                $categoryValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $categoryValidation->setAllowBlank(true);
                $categoryValidation->setShowDropDown(true);
                $categoryValidation->setFormula1('"PRODUCTION,SHOPPING"');

                // Data validation for Sub Category (Column D)
                $subCategoryValidation = new DataValidation();
                $subCategoryValidation->setType(DataValidation::TYPE_LIST);
                $subCategoryValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $subCategoryValidation->setAllowBlank(true);
                $subCategoryValidation->setShowDropDown(true);
                $subCategoryValidation->setFormula1('"Sol Potong,Sol Jadi,Foxing,Vibram"');

                // Data validation for Status (Column J)
                $statusValidation = new DataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST);
                $statusValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $statusValidation->setAllowBlank(false);
                $statusValidation->setShowDropDown(true);
                $statusValidation->setFormula1('"Ready,Belanja,Followup,Reject,Retur"');

                // Apply validations for rows 2 to 100
                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("B$i")->setDataValidation(clone $typeValidation);
                    $sheet->getCell("C$i")->setDataValidation(clone $categoryValidation);
                    $sheet->getCell("D$i")->setDataValidation(clone $subCategoryValidation);
                    $sheet->getCell("J$i")->setDataValidation(clone $statusValidation);
                }
            }
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CxFollowupExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithDrawings
{
    protected $data;
    protected $index = 1;
    protected $rowNumber = 2; // Row 1 is header
    protected $drawingsList = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'NO. SPK',
            'CUSTOMER',
            'WHATSAPP',
            'BRAND',
            'TGL MASUK',
            'EST. SELESAI',
            'SUMBER KENDALA',
            'KATEGORI KENDALA',
            'FOTO KENDALA', // Column J (10th column)
            'DETAIL KENDALA (ISSUE)',
            'OPSI SOLUSI',
            'STATUS PENGIRIMAN',
            'HANDLER CX',
            'STATUS RESOLUSI',
            'CATATAN RESOLUSI',
            'TANGGAL RESOLUSI',
        ];
    }

    /**
     * @param mixed $item
     * @return array
     */
    public function map($item): array
    {
        if ($item instanceof \App\Models\WorkOrder) {
            $workOrder = $item;
            $openIssue = $workOrder->cxIssues->first();
        } else {
            $openIssue = $item;
            $workOrder = $item->workOrder;
        }

        $spk = $workOrder->spk_number ?? '-';
        $customerName = $workOrder->customer_name ?? ($openIssue->customer_name ?? '-');
        $customerPhone = $workOrder->customer_phone ?? ($openIssue->customer_phone ?? '-');
        $brand = $workOrder->shoe_brand ?? '-';
        $entryDate = $workOrder->entry_date ? $workOrder->entry_date->format('Y-m-d H:i') : '-';
        
        $estDate = '-';
        if ($workOrder) {
            if ($workOrder->new_estimation_date) {
                $estDate = $workOrder->new_estimation_date->format('Y-m-d');
            } elseif ($workOrder->estimation_date) {
                $estDate = $workOrder->estimation_date->format('Y-m-d');
            }
        }

        $source = $openIssue->source ?? '-';
        $category = $openIssue->category ?? '-';
        $description = $openIssue->description ?? ($openIssue->kendala ?? '-');
        $options = $openIssue->opsi_solusi ?? '-';
        $shipping = $openIssue->shipping_status ?? 'SEND';
        $handler = ($workOrder && $workOrder->cxHandler) ? $workOrder->cxHandler->name : 'Unassigned';
        $status = $openIssue->status ?? 'OPEN';
        $resolutionNotes = $openIssue->resolution_notes ?? '-';
        $resolvedAt = ($openIssue && $openIssue->resolved_at) ? $openIssue->resolved_at->format('Y-m-d H:i') : '-';

        // Add drawing if photo exists
        if ($openIssue && $openIssue->photos && is_array($openIssue->photos) && count($openIssue->photos) > 0) {
            $photoPath = $openIssue->photos[0];
            if (str_starts_with($photoPath, 'storage/')) {
                $photoPath = substr($photoPath, 8);
            }
            $fullPath = storage_path('app/public/' . $photoPath);
            if (file_exists($fullPath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Foto Kendala');
                $drawing->setDescription('Foto Kendala');
                $drawing->setPath($fullPath);
                $drawing->setHeight(50); // Set height to 50px
                $drawing->setCoordinates('J' . $this->rowNumber);
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(8);
                $this->drawingsList[] = $drawing;
            }
        }

        $this->rowNumber++;

        return [
            $this->index++,
            $spk,
            $customerName,
            $customerPhone,
            $brand,
            $entryDate,
            $estDate,
            $source,
            $category,
            "", // Column J placeholder for image drawing
            $description,
            $options,
            $shipping,
            $handler,
            $status,
            $resolutionNotes,
            $resolvedAt,
        ];
    }

    /**
     * @return array
     */
    public function drawings()
    {
        return $this->drawingsList;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Styling header
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1:Q1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E2F0D9'); // Light green accent header

        // Alignments and padding
        $sheet->getStyle('A:Q')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('K:L')->getAlignment()->setWrapText(true);
        
        // Center text formatting for status columns
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F:G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:I')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('O')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('Q')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Adjust row heights to fit drawings comfortably (50px height + padding = 66pt)
        for ($i = 2; $i < $this->rowNumber; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(66);
        }

        // Set column J width explicitly to fit the 50px high image
        $sheet->getColumnDimension('J')->setWidth(18);

        return [];
    }
}

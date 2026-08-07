<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KpiFinanceExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $startLabel;
    protected $endLabel;

    public function __construct(array $data, string $startLabel, string $endLabel)
    {
        $this->data = $data;
        $this->startLabel = $startLabel;
        $this->endLabel = $endLabel;
    }

    public function array(): array
    {
        $statusDist = $this->data['status_distribution'] ?? [];
        $paymentTypeDist = $this->data['payment_type_distribution'] ?? [];

        $rows = [
            // Row 1: Title
            ['SHOE WORKSHOP - LAPORAN RINGKASAN KPI FINANCE'],
            // Row 2: Date Info
            ['Periode Analisis:', $this->startLabel . ' s/d ' . $this->endLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Section 1 Header
            ['METRIK KEUANGAN PERIODE AKTIF', 'NILAI NOMINAL', 'KETERANGAN'],
            ['Total Nilai Tagihan (Invoiced)', 'Rp ' . number_format($this->data['total_invoiced'], 0, ',', '.'), 'Total tagihan invoice periode aktif'],
            ['Kas Masuk Tervalidasi', 'Rp ' . number_format($this->data['cash_received'], 0, ',', '.'), 'Realisasi penerimaan pembayaran periode aktif'],
            ['Sisa Piutang Aktif', 'Rp ' . number_format($this->data['active_receivables'], 0, ',', '.'), 'Sisa piutang tagihan periode aktif'],
            ['Rasio Penagihan (Collection Rate)', $this->data['collection_rate'] . '%', 'Efektivitas cash flow periode aktif'],
            ['Realisasi Omzet (Omset Closing Valid)', 'Rp ' . number_format($this->data['revenue_realization'], 0, ',', '.'), 'Nilai closing transaksi valid periode aktif'],
            ['Total Diskon Diberikan', 'Rp ' . number_format($this->data['total_discount'] ?? 0, 0, ',', '.'), 'Total nominal potongan harga invoice periode aktif'],
            
            [''],
            
            // Row 12: Section 2 Header
            ['DISTRIBUSI STATUS TAGIHAN (PERIODE AKTIF)', 'JUMLAH TRANSAKSI', 'TOTAL NOMINAL'],
            ['Belum Bayar', ($statusDist['belum_bayar']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($statusDist['belum_bayar']['total'] ?? 0, 0, ',', '.')],
            ['DP / Cicil', ($statusDist['dp_cicil']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($statusDist['dp_cicil']['total'] ?? 0, 0, ',', '.')],
            ['Lunas', ($statusDist['lunas']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($statusDist['lunas']['total'] ?? 0, 0, ',', '.')],

            [''],

            // Row 17: Section 3 Header
            ['DISTRIBUSI TIPE PEMBAYARAN (PERIODE AKTIF)', 'JUMLAH TRANSAKSI', 'TOTAL NOMINAL'],
            ['DP Awal (BEFORE)', ($paymentTypeDist['dp_awal']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($paymentTypeDist['dp_awal']['total'] ?? 0, 0, ',', '.')],
            ['Pelunasan (AFTER)', ($paymentTypeDist['pelunasan']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($paymentTypeDist['pelunasan']['total'] ?? 0, 0, ',', '.')],
            ['Tambah Jasa', ($paymentTypeDist['tambah_jasa']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($paymentTypeDist['tambah_jasa']['total'] ?? 0, 0, ',', '.')],
            ['Lunas Awal', ($paymentTypeDist['lunas_awal']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($paymentTypeDist['lunas_awal']['total'] ?? 0, 0, ',', '.')],
            ['Ongkos Kirim', ($paymentTypeDist['ongkir']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($paymentTypeDist['ongkir']['total'] ?? 0, 0, ',', '.')],
            ['Pembayaran OTO', ($paymentTypeDist['oto']['count'] ?? 0) . ' Transaksi', 'Rp ' . number_format($paymentTypeDist['oto']['total'] ?? 0, 0, ',', '.')],

            [''],
            ['Laporan di-generate pada:', date('d/m/Y H:i')],
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:C1');
        $totalRows = $sheet->getHighestRow();

        $sheet->getStyle('A4:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A12:C12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A17:C17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '166534']]],
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '475569']]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']] // Teal-600
            ],
            12 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']] // Sky-600
            ],
            17 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']] // Indigo-600
            ],
            ($totalRows) => ['font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']]]
        ];
    }
}

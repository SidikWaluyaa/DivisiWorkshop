<?php

namespace App\Enums;

enum WorkOrderStatus: string
{
    case SPK_PENDING = 'SPK_PENDING'; // Order from CS, waiting for Warehouse Check
    case DITERIMA = 'DITERIMA';

    case ASSESSMENT = 'ASSESSMENT'; // Workshop checks and defines services
    case PREPARATION = 'PREPARATION'; // Cuci, Sol, Upper tasks
    case SORTIR = 'SORTIR'; // Material check
    case PRODUCTION = 'PRODUCTION';
    case QC = 'QC';
    case SELESAI = 'SELESAI';
    case DIANTAR = 'DIANTAR'; // If delivery is needed
    case HOLD_FOR_CX = 'HOLD_FOR_CX'; // Legacy?
    case CX_FOLLOWUP = 'CX_FOLLOWUP'; // New Standard

    case BATAL = 'BATAL';

    case WAITING_PAYMENT = 'WAITING_PAYMENT'; // Assessment Selesai, Menunggu Pembayaran/Approval
    case DONASI = 'DONASI'; // Status Hangus / Donasi

    public function label(): string
    {
        return match($this) {
            self::SPK_PENDING => 'Pending (CS)',
            self::DITERIMA => 'Diterima Gudang',
            
            self::HOLD_FOR_CX => 'Menunggu Konfirmasi CX', // Legacy
            self::CX_FOLLOWUP => 'CX Follow Up', // New

            self::ASSESSMENT => 'Assessment Workshop',
            self::WAITING_PAYMENT => 'Menunggu Pembayaran',
            self::PREPARATION => 'Preparation',
            self::SORTIR => 'Sortir & Material',
            self::PRODUCTION => 'Production',
            self::QC => 'Quality Control',
            self::SELESAI => 'Selesai',
            self::DIANTAR => 'Sedang Diantar',
            self::DONASI => 'Donasi / Hangus',
            self::BATAL => 'Batal',
        };
    }
}

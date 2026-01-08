<?php

namespace App\Enums;

enum WorkOrderStatus: string
{
    case DITERIMA = 'DITERIMA';

    case ASSESSMENT = 'ASSESSMENT'; // Workshop checks and defines services
    case PREPARATION = 'PREPARATION'; // Cuci, Sol, Upper tasks
    case SORTIR = 'SORTIR'; // Material check
    case PRODUCTION = 'PRODUCTION';
    case QC = 'QC';
    case SELESAI = 'SELESAI';
    case DIANTAR = 'DIANTAR'; // If delivery is needed
    case BATAL = 'BATAL';

    public function label(): string
    {
        return match($this) {
            self::DITERIMA => 'Diterima Gudang',

            self::ASSESSMENT => 'Assessment Workshop',
            self::PREPARATION => 'Preparation',
            self::SORTIR => 'Sortir & Material',
            self::PRODUCTION => 'Production',
            self::QC => 'Quality Control',
            self::SELESAI => 'Selesai',
            self::DIANTAR => 'Sedang Diantar',
            self::BATAL => 'Batal',
        };
    }
}

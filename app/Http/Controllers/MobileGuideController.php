<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MobileGuideController extends Controller
{
    /**
     * Tampilkan atau unduh PDF Panduan Ringkas Scan QR Mobile Teknisi (1 Halaman Laminating Meja Kerja).
     */
    public function downloadPdf(Request $request)
    {
        $logoPath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $pdf = Pdf::loadView('mobile.pdf.quick-guide', compact('logoBase64'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);

        $fileName = 'PANDUAN_RINGKAS_QR_SCAN_MOBILE_TEKNISI.pdf';

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }
}


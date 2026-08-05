<table>
    <!-- Header Title -->
    <tr>
        <th colspan="9" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #22AF85; color: #ffffff; height: 35px;">
            LAPORAN TRANSAKSI SPK CS
        </th>
    </tr>
    <!-- Filter Meta info -->
    <tr>
        <th colspan="9" style="font-size: 10px; text-align: center; color: #64748b; height: 20px;">
            Periode: 
            @if(request('date_from') && request('date_to'))
                {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }} s/d {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
            @elseif(request('date_from'))
                Mulai {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
            @elseif(request('date_to'))
                Sampai {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
            @else
                Semua Periode Tanggal
            @endif
            | Status: {{ request('status') ? str_replace('_', ' ', request('status')) : 'Semua Status' }}
            | Dicetak: {{ now()->format('d M Y H:i') }}
        </th>
    </tr>
    <tr><td colspan="9" style="height: 10px;"></td></tr>

    <!-- Summary Metrics -->
    <tr>
        <td colspan="3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; font-weight: bold; color: #64748b; height: 25px;">TOTAL SPK DIBUAT:</td>
        <td colspan="6" style="background-color: #f8fafc; border: 1px solid #e2e8f0; font-weight: bold; color: #0f172a;">{{ $totalSpk }} SPK</td>
    </tr>
    <tr>
        <td colspan="3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; font-weight: bold; color: #64748b; height: 25px;">TOTAL NILAI TRANSAKSI (OMZET):</td>
        <td colspan="6" style="background-color: #f8fafc; border: 1px solid #e2e8f0; font-weight: bold; color: #0f172a; text-align: left;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
    </tr>
    <tr><td colspan="9" style="height: 15px;"></td></tr>

    <!-- Table Headings -->
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: center; width: 5px;">No</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; width: 25px;">No SPK</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; width: 18px;">Tanggal</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; width: 25px;">Nama Customer</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; width: 15px;">No Telepon</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; width: 45px;">Detail Item &amp; Jasa</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: right; width: 18px;">Total Transaksi</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: right; width: 15px;">DP Amount</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: center; width: 18px;">Status SPK</th>
        </tr>
    </thead>

    <!-- Table Body -->
    <tbody>
        @foreach($spks as $index => $spk)
            <tr>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: top;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; color: #22AF85; vertical-align: top;">{{ $spk->spk_number }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: top;">{{ $spk->created_at->format('d M Y H:i') }}</td>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; vertical-align: top;">{{ $spk->lead?->customer_name ?? 'Unknown Customer' }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: top;">{{ $spk->lead?->customer_phone ?? '-' }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: top;">
                    @php
                        $itemsDetails = [];
                        if ($spk->items && count($spk->items) > 0) {
                            foreach ($spk->items as $item) {
                                $services = '';
                                if (is_array($item->services)) {
                                    $services = collect($item->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ');
                                } else {
                                    $services = $item->services;
                                }
                                $itemsDetails[] = $item->shoe_brand . " (" . $item->shoe_type . ") - " . $services;
                            }
                        } elseif ($spk->shoe_brand) {
                            $services = '';
                            if (is_array($spk->services)) {
                                $services = collect($spk->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ');
                            } else {
                                $services = $spk->services;
                            }
                            $itemsDetails[] = $spk->shoe_brand . " (" . $spk->shoe_type . ") - " . $services;
                        }
                    @endphp
                    {{ implode(', ', $itemsDetails) }}
                </td>
                <td style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold; vertical-align: top;">{{ (float) $spk->total_price }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: right; vertical-align: top;">{{ (float) $spk->dp_amount }}</td>
                
                <!-- Conditional Formatting for Status SPK -->
                @php
                    $bgColor = '#f1f5f9';
                    $textColor = '#475569';
                    if ($spk->status === 'DP_PAID') {
                        $bgColor = '#dcfce7';
                        $textColor = '#15803d';
                    } elseif ($spk->status === 'WAITING_DP') {
                        $bgColor = '#fef9c3';
                        $textColor = '#a16207';
                    } elseif ($spk->status === 'HANDED_TO_WORKSHOP') {
                        $bgColor = '#e0f2fe';
                        $textColor = '#0369a1';
                    }
                @endphp
                <td style="border: 1px solid #cbd5e1; background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: bold; text-align: center; vertical-align: top;">
                    {{ $spk->label }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

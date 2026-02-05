<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @php
        $model = $spk ?? $quotation;
        $lead = $model->lead ?? null;
        
        $spkNumber = $spk->spk_number ?? ($lead->spk->spk_number ?? null);
        $displayNumber = $spkNumber ?? $quotation->quotation_number ?? 'BELUM ADA SPK';
        
        $customerName = $lead->customer_name ?? $model->customer_name ?? '-';
        $customerPhone = $lead->customer_phone ?? $model->customer_phone ?? '-';
        
        $addrParts = [];
        if ($lead->customer_address) $addrParts[] = $lead->customer_address;
        if ($lead->customer_city) $addrParts[] = $lead->customer_city;
        if ($lead->customer_province) $addrParts[] = $lead->customer_province;
        $customerFullAddress = !empty($addrParts) ? implode(', ', $addrParts) : ($model->customer_address ?? '-');
        
        $csName = $lead->cs->name ?? $cs_name ?? 'Admin';
        
        // Workshop as Penerima (Recipient)
        $workshopName = "SHOEWORKSHOP (" . $csName . ")";
        $workshopAddress = "Jl. Kembar I No.41, Cigereleng, Kec. Regol, Kota Bandung, Jawa Barat 40253";
        $workshopPhone = "0895339939800";
    @endphp
    <title>Resi Pengiriman - {{ $displayNumber }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            background: #fff;
            width: 100%;
        }

        .page-container {
            width: 210mm;
            height: 297mm;
            position: relative;
        }

        /* Airmail Styled Resi - Top Half A4 */
        .airmail-resi {
            position: absolute;
            top: 20mm;
            left: 20mm;
            right: 20mm;
            height: 108.5mm; /* Precise top-half within margins */
            padding: 8px; /* For the border stripes */
            background: repeating-linear-gradient(
                -45deg,
                #22AF85,
                #22AF85 20px,
                #fff 20px,
                #fff 40px,
                #FFC232 40px,
                #FFC232 60px,
                #fff 60px,
                #fff 80px
            );
            border-radius: 4px;
        }

        .envelope-body {
            width: 100%;
            height: 100%;
            background: #fffdf5; /* Creamy envelope color */
            border-radius: 2px;
            padding: 30px;
            position: relative;
        }

        /* Minimal Header/Branding for Logistics */
        .id-strip {
            position: absolute;
            top: 15px;
            right: 25px;
            text-align: right;
        }
        .spk-label {
            font-size: 8px;
            color: #22AF85;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
        }
        .spk-id {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
        }

        /* Content Blocks as per User Image */
        .recipient-block {
            width: 60%;
            margin-bottom: 20px;
        }
        .sender-block {
            width: 60%;
            float: right;
            text-align: left;
            margin-top: 10px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #1e293b;
        }
        .entity-name {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .entity-address {
            font-size: 11px;
            color: #475569;
            line-height: 1.4;
            font-weight: 500;
        }
        .entity-phone {
            font-size: 11px;
            color: #1e293b;
            font-weight: 700;
            margin-top: 4px;
        }

        /* Branding subtly integrated */
        .logo-footer {
            position: absolute;
            bottom: 20px;
            left: 30px;
            font-size: 12px;
            font-weight: 900;
            color: #22AF85;
            letter-spacing: 3px;
        }

        .timestamp {
            position: absolute;
            bottom: 20px;
            right: 30px;
            font-size: 8px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Physical Cut Guideline */
        .cut-line {
            position: absolute;
            top: 148.5mm;
            left: 0;
            width: 100%;
            border-bottom: 1px dashed #cbd5e1;
            text-align: center;
        }
        .cut-label {
            background: #ffffff;
            padding: 2px 12px;
            font-size: 8px;
            color: #cbd5e1;
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="airmail-resi">
            <div class="envelope-body clearfix">
                {{-- Logistics ID --}}
                <div class="id-strip">
                    <div class="spk-label">Nomor SPK Utama</div>
                    <div class="spk-id">{{ $displayNumber }}</div>
                </div>

                {{-- Recipient Section (Top Left) --}}
                <div class="recipient-block">
                    <div class="section-title">Nama Penerima:</div>
                    <div class="entity-name">{{ $workshopName }}</div>
                    <div class="entity-address">{{ $workshopAddress }}</div>
                    <div class="entity-phone">Nomor Telepon/WA: {{ $workshopPhone }}</div>
                </div>

                {{-- Sender Section (Bottom Right) --}}
                <div class="sender-block">
                    <div class="section-title">Nama Pengirim:</div>
                    <div class="entity-name">{{ $customerName }}</div>
                    <div class="entity-address">{{ $customerFullAddress }}</div>
                    <div class="entity-phone">Nomor Telepon/WA: {{ $customerPhone }}</div>
                </div>

                {{-- Subtle Branding --}}
                <div class="logo-footer">SHOEWORKSHOP</div>
                <div class="timestamp">Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</div>
            </div>
        </div>

        {{-- Cut Guide --}}
        <div class="cut-line">
            <span class="cut-label">Gunting Di Sini / Cut Line</span>
        </div>
    </div>
</body>
</html>

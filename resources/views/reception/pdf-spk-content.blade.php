<table>
    <tr>
        <!-- SIDEBAR -->
        <td class="sidebar">
            <div class="sidebar-inner">
                <div class="logo-section">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 60%;">
                                <div style="font-weight: 900; font-size: 14px;">SHOE WORKSHOP</div>
                                <div style="font-size: 10px; opacity: 0.8;">Form SPK Customer</div>
                            </td>
                            <td style="width: 40%; text-align: right;">
                                <div class="qr-code">
                                    <img src="{{ $barcode }}" width="60" height="60">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="sidebar-label">Detail Sepatu</div>
                <div class="sidebar-box">
                    <strong>Brand:</strong> {{ $order->shoe_brand }}<br>
                    <strong>Type:</strong> {{ $order->shoe_type }}<br>
                    <strong>Color:</strong> {{ $order->shoe_color }}<br>
                    <strong>Size:</strong> {{ $order->shoe_size }}
                </div>

                <div class="sidebar-label">Catatan Gudang</div>
                <div class="sidebar-box" style="min-height: 100px;">
                    @if($order->technician_notes)
                        @foreach(explode("\n", $order->technician_notes) as $line)
                            @if(trim($line))
                                • {{ trim($line) }}<br>
                            @endif
                        @endforeach
                    @else
                        <span style="opacity: 0.5;">- Tidak ada catatan -</span>
                    @endif
                </div>

                <div class="acc-qc-box">
                    <div class="acc-qc-header">ACC QC</div>
                    <div class="acc-qc-body">
                        <div style="font-size: 8px; margin-bottom: 5px;">REVISI:</div>
                        <div style="height: 40px; border-bottom: 1px solid rgba(255,255,255,0.2);"></div>
                        <table style="margin-top: 10px;">
                            <tr>
                                <td style="font-size: 8px;">PARAF QC:</td>
                                <td style="text-align: right;">
                                    <div style="width: 40px; height: 40px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.05);"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="footer-sidebar">
                    <div class="footer-tag">
                        #<span style="color: #FFC232;">living</span>with<br>
                        <span style="font-size: 20px;">PASSION</span>
                    </div>
                </div>
            </div>
        </td>

        <!-- MAIN CONTENT -->
        <td class="main-content">
            <table style="margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; padding-right: 10px;">
                        <div class="main-label">Nomor SPK</div>
                        <div class="main-box">
                            <span class="spk-number">{{ $order->spk_number }}</span>
                        </div>
                    </td>
                    <td style="width: 50%; padding-left: 10px;">
                        <div class="main-label">Nama Customer</div>
                        <div class="main-box">
                            <span style="font-weight: bold; font-size: 14px;">{{ $order->customer_name }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="main-label">Alamat Lengkap</div>
                        <div class="main-box">
                            <div style="font-size: 11px;">
                                {{ $order->customer_address }}
                            </div>
                        </td>
                    </td>
                </tr>
            </table>

            <div class="main-label" style="margin-bottom: 10px;">Jasa Pengerjaan:</div>
            @foreach($order->workOrderServices as $service)
                <div class="orange-bar">
                    {{ strtoupper($service->custom_service_name ?? $service->service->name ?? 'Service') }} | 
                    {{ strtoupper($service->category_name ?? ($service->service ? $service->service->category : 'S')) }}
                </div>
                <div class="service-detail">
                    @if(is_array($service->service_details))
                        @foreach($service->service_details as $key => $val)
                            @if(is_array($val))
                                @foreach($val as $line)
                                    • {{ strtoupper($line) }}<br>
                                @endforeach
                            @else
                                • {{ strtoupper($val) }}<br>
                            @endif
                        @endforeach
                    @endif
                    @if(!empty($service->notes))
                        <div style="font-style: italic; color: #64748b; margin-top: 5px;">{{ $service->notes }}</div>
                    @endif
                </div>
            @endforeach

            <div style="margin-top: 40px;">
                <table class="signature-grid">
                    <tr>
                        <td style="width: 33%; padding: 5px;">
                            <div class="signature-box">
                                <div style="font-weight: bold; margin-bottom: 5px;">SPK MASUK</div>
                                <div style="margin-top: 15px; border-bottom: 1px dotted #ccc; width: 80%; margin-left: 10%;"></div>
                            </div>
                        </td>
                        <td style="width: 33%; padding: 5px;">
                            <div class="signature-box" style="background-color: #f0fdfa; border-color: #ccfbf1;">
                                <div style="font-weight: bold; margin-bottom: 5px; color: #134e4a;">ESTIMASI SELESAI</div>
                                @if($order->invoice && $order->invoice->estimasi_selesai)
                                    <div style="font-size: 11px; font-weight: bold; text-align: center; color: #111827; margin-top: 5px; text-transform: uppercase;">
                                        {{ \Carbon\Carbon::parse($order->invoice->estimasi_selesai)->translatedFormat('d M Y') }}
                                    </div>
                                @else
                                    <div style="margin-top: 15px; border-bottom: 1px dotted #99f6e4; width: 80%; margin-left: 10%;"></div>
                                @endif
                            </div>
                        </td>
                        <td style="width: 33%; padding: 5px;">
                            <div class="signature-box">
                                <div style="font-weight: bold; margin-bottom: 5px;">SPK KELUAR</div>
                                <div style="margin-top: 15px; border-bottom: 1px dotted #ccc; width: 80%; margin-left: 10%;"></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-top: 20px; border: 1px dashed #e2e8f0; border-radius: 8px; padding: 10px; min-height: 120px; position: relative;">
                <div style="font-size: 9px; font-weight: bold; color: #94a3b8; text-transform: uppercase;">Note</div>
            </div>

            <div style="margin-top: 30px; text-align: center; opacity: 0.3; font-size: 10px; font-weight: bold; color: #22B086;">
                SHOE WORKSHOP PREMIUM - #MORETHANREPAIR
            </div>
        </td>
    </tr>
</table>

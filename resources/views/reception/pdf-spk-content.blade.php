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
                    @if(!empty($service->service_details['is_cx_additional']) && $service->service_details['is_cx_additional'])
                        <span style="background-color: #f59e0b; color: #fff; padding: 1px 4px; border-radius: 3px; font-weight: bold; font-size: 8px; margin-right: 5px; display: inline-block; vertical-align: middle;">[JASA TAMBAHAN]</span>
                    @endif
                    {{ strtoupper($service->custom_service_name ?? $service->service->name ?? 'Service') }} | 
                    {{ strtoupper($service->category_name ?? ($service->service ? $service->service->category : 'S')) }}
                </div>
                <div class="service-detail">
                    @if(is_array($service->service_details))
                        @foreach($service->service_details as $key => $val)
                            @if($key !== 'is_cx_additional' && $key !== 'hk_days')
                                @if(is_array($val))
                                    @foreach($val as $line)
                                        • {{ strtoupper($line) }}<br>
                                    @endforeach
                                @else
                                    • {{ strtoupper($val) }}<br>
                                @endif
                            @endif
                        @endforeach
                    @endif
                    @if(!empty($service->notes))
                        <div style="font-style: italic; color: #64748b; margin-top: 5px;">{{ $service->notes }}</div>
                    @endif
                </div>
            @endforeach

            {{-- Kolom Tambah Jasa & OTO (Manual / Tulis Tangan) --}}
            <div style="margin-top: 20px;">
                <table style="width: 100%; margin-bottom: 4px;">
                    <tr>
                        <td style="font-size: 9px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px;">
                            TAMBAH JASA & OTO (MANUAL / TULIS TANGAN)
                        </td>
                        <td style="text-align: right; font-size: 8px; font-weight: bold; color: #64748b;">
                            [ ISI OLEH WORKSHOP / TEKNISI ]
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                    <thead>
                        <tr style="background-color: #f1f5f9; color: #334155; font-size: 8.5px; text-transform: uppercase; font-weight: bold;">
                            <th style="padding: 5px 8px; text-align: left; border-bottom: 1px solid #cbd5e1; width: 55%;">Deskripsi Jasa Tambahan / Item OTO</th>
                            <th style="padding: 5px 8px; text-align: center; border-bottom: 1px solid #cbd5e1; width: 25%;">Biaya / Nominal</th>
                            <th style="padding: 5px 8px; text-align: center; border-bottom: 1px solid #cbd5e1; width: 20%;">Paraf / PIC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px dotted #cbd5e1; border-right: 1px solid #f1f5f9; vertical-align: bottom; height: 18px;">
                                <span style="font-size: 9px; color: #cbd5e1; font-weight: bold;">1.</span>
                            </td>
                            <td style="padding: 8px; border-bottom: 1px dotted #cbd5e1; border-right: 1px solid #f1f5f9; text-align: left; vertical-align: bottom;">
                                <span style="font-size: 9px; color: #cbd5e1;">Rp</span>
                            </td>
                            <td style="padding: 8px; border-bottom: 1px dotted #cbd5e1; text-align: center; vertical-align: bottom;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px dotted #cbd5e1; border-right: 1px solid #f1f5f9; vertical-align: bottom; height: 18px;">
                                <span style="font-size: 9px; color: #cbd5e1; font-weight: bold;">2.</span>
                            </td>
                            <td style="padding: 8px; border-bottom: 1px dotted #cbd5e1; border-right: 1px solid #f1f5f9; text-align: left; vertical-align: bottom;">
                                <span style="font-size: 9px; color: #cbd5e1;">Rp</span>
                            </td>
                            <td style="padding: 8px; border-bottom: 1px dotted #cbd5e1; text-align: center; vertical-align: bottom;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-right: 1px solid #f1f5f9; vertical-align: bottom; height: 18px;">
                                <span style="font-size: 9px; color: #cbd5e1; font-weight: bold;">3.</span>
                            </td>
                            <td style="padding: 8px; border-right: 1px solid #f1f5f9; text-align: left; vertical-align: bottom;">
                                <span style="font-size: 9px; color: #cbd5e1;">Rp</span>
                            </td>
                            <td style="padding: 8px; text-align: center; vertical-align: bottom;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 18px;">
                <table class="signature-grid">
                    <tr>
                        <td style="width: 33%; padding: 3px;">
                            <div class="signature-box">
                                <div style="font-weight: bold; margin-bottom: 4px; font-size: 9px;">SPK MASUK :</div>
                                <div style="margin-top: 15px; border-bottom: 1px dotted #94a3b8; width: 85%; margin-left: 7.5%;"></div>
                            </div>
                        </td>
                        <td style="width: 33%; padding: 3px;">
                            <div class="signature-box" style="background-color: #f0fdfa; border-color: #ccfbf1;">
                                <div style="font-weight: bold; margin-bottom: 4px; color: #134e4a; font-size: 9px;">ESTIMASI SELESAI :</div>
                                @if($order->invoice && $order->invoice->estimasi_selesai)
                                    <div style="font-size: 10px; font-weight: bold; text-align: center; color: #111827; margin-top: 4px; text-transform: uppercase;">
                                        {{ \Carbon\Carbon::parse($order->invoice->estimasi_selesai)->translatedFormat('d M Y') }}
                                    </div>
                                @else
                                    <div style="margin-top: 15px; border-bottom: 1px dotted #99f6e4; width: 85%; margin-left: 7.5%;"></div>
                                @endif
                            </div>
                        </td>
                        <td style="width: 33%; padding: 3px;">
                            <div class="signature-box">
                                <div style="font-weight: bold; margin-bottom: 4px; font-size: 9px;">SPK KELUAR :</div>
                                <div style="margin-top: 15px; border-bottom: 1px dotted #94a3b8; width: 85%; margin-left: 7.5%;"></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Note / Catatan Tambahan dengan Dotted Lines untuk Tulis Tangan --}}
            <div style="margin-top: 14px; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 8px 10px; position: relative; background-color: #ffffff;">
                <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 4px;">NOTE / CATATAN TAMBAHAN</div>
                <div style="border-bottom: 1px dotted #cbd5e1; height: 18px;"></div>
                <div style="border-bottom: 1px dotted #cbd5e1; height: 18px;"></div>
                <div style="border-bottom: 1px dotted #cbd5e1; height: 18px;"></div>
            </div>

            <div style="margin-top: 20px; text-align: center; opacity: 0.5; font-size: 9.5px; font-weight: bold; color: #22B086; letter-spacing: 0.5px;">
                SHOE WORKSHOP PREMIUM - #MORETHANREPAIR
            </div>
        </td>
    </tr>
</table>

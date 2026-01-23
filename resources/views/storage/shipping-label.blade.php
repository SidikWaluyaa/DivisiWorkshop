<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label - {{ $assignment->workOrder->spk_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 15cm 12cm;
            margin: 0;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            width: 15cm;
            height: 12cm;
            position: relative;
            background-color: #f8f9fa;
            overflow: hidden;
            color: #333;
            /* Marble Texture Simulation */
            background-image: 
                linear-gradient(135deg, rgba(0,0,0,0.03) 25%, transparent 25%),
                linear-gradient(225deg, rgba(0,0,0,0.03) 25%, transparent 25%),
                linear-gradient(45deg, rgba(0,0,0,0.03) 25%, transparent 25%),
                radial-gradient(circle at 10% 20%, rgba(0,0,0,0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0,0,0,0.05) 0%, transparent 20%);
            background-size: 20px 20px, 20px 20px, 20px 20px, 100% 100%, 100% 100%;
        }

        /* Abstract Shapes */
        .shape-yellow-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 320px;
            height: 140px;
            background-color: #FFC107;
            border-bottom-right-radius: 140px;
            z-index: 1;
        }

        .shape-green-left {
            position: absolute;
            top: 140px;
            left: 0;
            width: 180px;
            height: 140px;
            background-color: #00A859;
            border-bottom-right-radius: 140px;
            border-top-right-radius: 40px;
            z-index: 2;
        }

        .shape-green-bottom {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 140px;
            height: 50px;
            background-color: #00A859;
            border-top-left-radius: 50px;
            z-index: 3;
        }
        
        .shape-yellow-bottom {
            position: absolute;
            bottom: 50px;
            right: 0;
            width: 80px;
            height: 50px;
            background-color: #FFC107;
            z-index: 1;
        }

        /* Content Container */
        .container {
            position: relative;
            z-index: 10;
            padding: 40px;
            height: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        /* Heading */
        .main-header {
            text-align: right;
            margin-bottom: auto; /* Push content down */
            position: relative;
            padding-right: 20px;
            padding-top: 20px;
        }
        
        .main-header h1 {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.1;
            margin: 0;
            color: #222;
        }

        .main-header .subtitle {
            margin-top: 12px;
            font-size: 11px;
            color: #555;
            text-align: left;
            display: inline-block;
            position: relative;
            padding-top: 8px;
        }
        
        .main-header .subtitle::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(to right, #00A859 40%, #FFC107 40%);
        }

        /* Address Box */
        .address-box {
            position: relative;
            width: 100%;
            height: 220px; /* Fixed height for the box portion */
            border: 2px solid #FFC107;
            background: white;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .address-tag {
            position: absolute;
            top: -15px;
            left: -2px; /* Slight overlap */
            background-color: #00A859;
            color: white;
            padding: 6px 20px;
            font-size: 11px;
            font-weight: 500;
            border-top-left-radius: 10px;
            border-bottom-right-radius: 25px;
            z-index: 20;
        }

        .address-content {
            padding: 25px 20px 10px 20px;
            flex: 1;
        }

        .customer-name {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .customer-address {
            font-size: 14px;
            line-height: 1.4;
            color: #444;
            max-width: 80%;
        }
        
        .customer-phone {
            margin-top: 5px;
            font-size: 13px;
            color: #00A859;
            font-weight: 600;
        }

        /* Footer inside Box */
        .box-footer {
            border-top: 2px solid #FFC107;
            height: 70px; /* Fixed footer height */
            display: flex;
            align-items: stretch;
        }

        .footer-logo {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 15px;
            border-right: 1px solid #FFC107;
        }
        
        .logo-img {
            max-height: 40px;
            max-width: 100%;
        }

        .footer-info {
            flex: 1.5;
            padding: 8px 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-size: 9px;
            color: #333;
            border-right: 1px solid #FFC107;
        }

        .footer-social {
            flex: 2;
            padding: 8px 15px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .social-col {
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 9px;
        }

        .info-title {
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 9px;
        }
        
        .flex-row {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Shapes -->
    <div class="shape-yellow-top"></div>
    <div class="shape-green-left"></div>
    <div class="shape-yellow-bottom"></div>
    <div class="shape-green-bottom"></div>

    <div class="container">
        <!-- Header -->
        <div class="main-header">
            <h1>Just<br>taking care<br>your shoes</h1>
            <div class="subtitle">
                our pleasure can be a part of<br>
                making your shoes better.
            </div>
        </div>

        <!-- Address Box with integrated Footer -->
        <div class="address-box">
            <div class="address-tag">To : our beloved customer</div>
            
            <div class="address-content">
                <div class="customer-name">{{ $assignment->workOrder->customer->name }}</div>
                <div class="customer-address">
                    {{ $assignment->workOrder->customer->address ?? '-' }}
                </div>
                <div class="customer-phone">
                    {{ $assignment->workOrder->customer->phone }}
                </div>
                @if(!$assignment->workOrder->customer->address)
                    <div style="font-size: 10px; color: #aaa; margin-top: 5px;">(Alamat tidak tersedia)</div>
                @endif
            </div>

            <div class="box-footer">
                <div class="footer-logo">
                    {{-- Base64 Embedded Logo --}}
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Shoe Workshop" class="logo-img">
                </div>
                
                <div class="footer-info">
                    <div class="info-title">Location</div>
                    <div>Jl. Kembar 1 No. 41</div>
                    <div>Cigereleng, Kec. Regol</div>
                    <div>Kota Bandung, 40253</div>
                    <div style="font-weight: 600; margin-top: 2px;">www.shoeworkshop.id</div>
                </div>

                <div class="footer-social">
                    <div class="social-col">
                        <div class="info-title">Stay Updated</div>
                        <div class="flex-row"><span>WA</span> <span>08877234545</span></div>
                        <div class="flex-row"><span>IG</span> <span>@shoe_workshop</span></div>
                        <div class="flex-row"><span>TT</span> <span>@shoe.workshop</span></div>
                    </div>
                    <div class="social-col">
                        <div class="info-title">Visit Our Media</div>
                        <div class="flex-row"><span>YT</span> <span>Shoe Police</span></div>
                        <div class="flex-row"><span>IG</span> <span>@shoepolice__</span></div>
                        <div class="flex-row"><span>TT</span> <span>@shoepolice__</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

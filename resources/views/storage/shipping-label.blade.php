<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label - {{ $assignment->workOrder->spk_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 15cm 12cm;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            width: 15cm;
            height: 12cm;
            position: relative;
            background: #e5e5e5;
            overflow: hidden;
            /* Marble Texture */
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(200,200,200,0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(180,180,180,0.2) 0%, transparent 50%),
                linear-gradient(135deg, rgba(0,0,0,0.02) 25%, transparent 25%),
                linear-gradient(225deg, rgba(0,0,0,0.02) 25%, transparent 25%);
            background-size: 100% 100%, 100% 100%, 15px 15px, 15px 15px;
        }

        /* Logo Decoration at Top */
        .logo-decoration {
            position: absolute;
            top: 0;
            left: 0;
            width: 350px;
            height: 300px;
            z-index: 1;
        }
        
        .logo-decoration img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: top left;
        }

        /* Bottom Decorative Shapes */
        .shape-green-bottom {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 145px;
            height: 52px;
            background: #00BFA5;
            border-top-left-radius: 52px;
            z-index: 3;
        }
        
        .shape-yellow-accent {
            position: absolute;
            bottom: 52px;
            right: 0;
            width: 85px;
            height: 52px;
            background: #FFC107;
            z-index: 2;
        }

        /* Main Container */
        .container {
            position: relative;
            z-index: 10;
            padding: 35px 40px 35px 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
            padding-top: 15px;
        }
        
        .tagline {
            text-align: left;
            max-width: 360px;
        }
        
        .tagline h1 {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.08;
            color: #2c2c2c;
            margin-bottom: 14px;
        }
        
        .tagline-underline {
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, #00BFA5 0%, #00BFA5 50%, #FFC107 50%, #FFC107 100%);
            margin-bottom: 10px;
        }
        
        .tagline p {
            font-size: 11px;
            color: #666;
            line-height: 1.55;
            font-weight: 400;
        }

        /* Address Container */
        .address-container {
            position: relative;
            margin-top: 0;
        }

        /* Address Box */
        .address-box {
            background: white;
            border: 2.5px solid #FFC107;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 220px;
        }

        .address-label {
            position: absolute;
            top: -16px;
            left: -3px;
            background: #00BFA5;
            color: white;
            padding: 7px 24px;
            font-size: 11px;
            font-weight: 500;
            border-top-left-radius: 12px;
            border-bottom-right-radius: 28px;
            letter-spacing: 0.3px;
        }

        .address-content {
            padding: 30px 24px 18px 24px;
            flex: 1;
        }

        .customer-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .customer-details {
            font-size: 13px;
            line-height: 1.65;
            color: #444;
            margin-bottom: 10px;
        }

        .customer-phone {
            font-size: 13px;
            color: #00BFA5;
            font-weight: 600;
        }

        /* Footer Section */
        .footer-section {
            border-top: 2.5px solid #FFC107;
            display: grid;
            grid-template-columns: 1fr 1.3fr 1.8fr;
            height: 76px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-right: 1px solid #e0e0e0;
        }

        .footer-logo img {
            max-height: 80px;
            max-width: 100%;
            object-fit: contain;
        }

        .footer-location {
            padding: 10px 16px;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .location-title {
            font-size: 9px;
            font-weight: 700;
            color: #333;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .location-icon {
            width: 10px;
            height: 10px;
        }

        .location-details {
            font-size: 8.5px;
            color: #555;
            line-height: 1.4;
        }

        .location-website {
            font-size: 8.5px;
            font-weight: 600;
            color: #333;
            margin-top: 2px;
        }

        .footer-social {
            padding: 10px 16px;
            display: flex;
            gap: 18px;
        }

        .social-column {
            flex: 1;
        }

        .social-title {
            font-size: 9px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .social-item {
            font-size: 8px;
            color: #555;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
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
    <!-- Logo Decoration -->
    <div class="logo-decoration">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Shoe Workshop">
    </div>
    
    <!-- Bottom Shapes -->
    <div class="shape-yellow-accent"></div>
    <div class="shape-green-bottom"></div>

    <div class="container">
        <!-- Header with Tagline -->
        <div class="header-section">
            <div class="tagline">
                <h1>Just<br>taking care<br>your shoes</h1>
                <div class="tagline-underline"></div>
                <p>our pleasure can be a part of<br>making your shoes better.</p>
            </div>
        </div>

        <!-- Address Box -->
        <div class="address-container">
            <div class="address-box">
                <div class="address-label">To : our beloved customer</div>
                
                <div class="address-content">
                    <div class="customer-name">{{ $assignment->workOrder->customer->name }}</div>
                    <div class="customer-details">
                        {{ $assignment->workOrder->customer->address ?? 'Alamat belum tersedia' }}
                    </div>
                    <div class="customer-phone">
                        📞 {{ $assignment->workOrder->customer->phone }}
                    </div>
                </div>

                <!-- Footer -->
                <div class="footer-section">
                    <!-- Logo -->
                    <div class="footer-logo">
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Shoe Workshop">
                    </div>

                    <!-- Location -->
                    <div class="footer-location">
                        <div class="location-title">
                            <svg class="location-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            Location
                        </div>
                        <div class="location-details">
                            Jl. Kembar 1 No. 41<br>
                            Cigereleng, Kec. Regol<br>
                            Kota Bandung, 40253
                        </div>
                        <div class="location-website">🌐 www.shoeworkshop.id</div>
                    </div>

                    <!-- Social Media -->
                    <div class="footer-social">
                        <div class="social-column">
                            <div class="social-title">Stay Updated</div>
                            <div class="social-item">
                                <span>📱</span> 08877234545
                            </div>
                            <div class="social-item">
                                <span>📷</span> shoe_workshop
                            </div>
                            <div class="social-item">
                                <span>🎵</span> shoe.workshop
                            </div>
                        </div>
                        <div class="social-column">
                            <div class="social-title">Visit Our Media</div>
                            <div class="social-item">
                                <span>▶️</span> Shoe Police
                            </div>
                            <div class="social-item">
                                <span>📷</span> shoepolice__
                            </div>
                            <div class="social-item">
                                <span>🎵</span> shoepolice__
                            </div>
                        </div>
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

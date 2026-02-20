<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sempurnakan Alamat Pengiriman | Shoe Workshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-green: #22AF85;
            --brand-yellow: #FFC232;
            --brand-dark: #1E293B;
            --brand-grey: #64748B;
            --bg-light: #FFFFFF;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: var(--brand-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }

        .main-card {
            background: var(--bg-light);
            border-radius: 2rem;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.03);
            width: 100%;
            max-width: 600px;
        }

        .input-brand {
            border: 1.5px solid #E2E8F0;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
        }

        .input-brand:focus {
            outline: none;
            border-color: var(--brand-green);
            box-shadow: 0 0 0 4px rgba(34, 175, 133, 0.08);
        }

        .label-brand {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--brand-grey);
            margin-bottom: 0.5rem;
            display: block;
        }

        .btn-brand {
            background-color: var(--brand-yellow);
            color: var(--brand-dark);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.25rem;
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            width: 100%;
            box-shadow: 0 5px 15px rgba(255, 194, 50, 0.3);
        }

        .btn-brand:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 194, 50, 0.5);
        }

        .btn-brand:active {
            transform: translateY(0);
        }

        .brand-text-accent {
            color: var(--brand-green);
        }

        select.input-brand {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 1rem;
        }

        /* Success State View */
        #success-state {
            display: none;
        }

        /* Animation class */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <div class="main-card overflow-hidden fade-in-up">
        {{-- Brand Header --}}
        <div class="pt-10 pb-6 px-8 text-center bg-slate-50/50">
            <img src="{{ asset('images/logo.png') }}" alt="Shoe Workshop Logo" class="h-16 mx-auto mb-6">
            <h1 class="text-2xl font-black text-slate-800 tracking-tight font-outfit uppercase">
                Verifikasi <span class="brand-text-accent">Alamat</span>
            </h1>
            <p class="text-slate-500 text-sm mt-2 font-medium">
                Pastikan pesanan Anda sampai ke tujuan dengan alamat yang akurat dan tervalidasi.
            </p>
        </div>

        {{-- Form Section --}}
        <div class="p-8 md:p-10" id="form-container">
            <form id="addressForm" class="space-y-6">
                <input type="hidden" name="token" id="token" value="{{ request()->segment(count(request()->segments())) }}">

                {{-- Name & Address --}}
                <div class="space-y-4">
                    <div>
                        <label class="label-brand">Nama Penerima</label>
                        <input type="text" id="name" readonly 
                               class="input-brand bg-slate-50 border-transparent text-slate-500 font-bold" value="Memuat nama...">
                    </div>

                    <div>
                        <label class="label-brand">Detail Alamat / Nama Jalan</label>
                        <textarea id="address" name="address" rows="3" required
                                  class="input-brand"
                                  placeholder="Contoh: Jl. Sudirman No. 123, Blok C4"></textarea>
                    </div>
                </div>

                {{-- Regional Selectors --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label-brand">Provinsi</label>
                        <select id="province_id" name="province_id" required class="input-brand text-sm">
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" id="province" name="province">
                    </div>

                    <div>
                        <label class="label-brand">Kota / Kabupaten</label>
                        <select id="city_id" name="city_id" required disabled class="input-brand text-sm">
                            <option value="">Pilih Kota</option>
                        </select>
                        <input type="hidden" id="city" name="city">
                    </div>

                    <div>
                        <label class="label-brand">Kecamatan</label>
                        <select id="district_id" name="district_id" required disabled class="input-brand text-sm">
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" id="district" name="district">
                    </div>

                    <div>
                        <label class="label-brand">Kelurahan</label>
                        <select id="village_id" name="village_id" required disabled class="input-brand text-sm">
                            <option value="">Pilih Kelurahan</option>
                        </select>
                        <input type="hidden" id="village" name="village">
                    </div>
                </div>

                {{-- Postal Code --}}
                <div>
                    <label class="label-brand">Kode Pos</label>
                    <input type="number" id="postal_code" name="postal_code" required
                           class="input-brand" placeholder="e.g. 53123">
                </div>

                {{-- Submit --}}
                <div class="pt-4">
                    <button type="submit" id="submitBtn" class="btn-brand">
                        Simpan Alamat Sekarang
                    </button>
                    <p class="text-[10px] text-center text-slate-400 mt-4 uppercase font-bold tracking-widest">
                        Secured by Shoe Workshop
                    </p>
                </div>
            </form>
        </div>

        {{-- Success Message --}}
        <div id="success-state" class="p-12 text-center bg-white">
            <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-emerald-100">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-black text-slate-800 font-outfit uppercase tracking-tighter mb-2">Alamat Berhasil<br><span class="brand-text-accent">Diperbarui</span></h2>
            <p class="text-slate-500 font-medium mb-12 leading-relaxed">Terima kasih. Informasi pengiriman Anda telah disinkronkan ke sistem pusat kami.</p>
            <button onclick="window.close()" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all">
                Tutup Jendela
            </button>
        </div>
    </div>

    <script>
        const API_URL = '/api/address_verification.php';
        const REGIONAL_PROXY = '/api/address_verification.php?proxy_path=';

        // Load Customer Initial Data
        async function fetchCustomerData() {
            const token = document.getElementById('token').value;
            try {
                const response = await fetch(`${API_URL}?token=${token}`);
                const res = await response.json();
                
                if (res.status === 'success') {
                    const data = res.data;
                    document.getElementById('name').value = data.name;
                    document.getElementById('address').value = data.address || '';
                    document.getElementById('postal_code').value = data.postal_code || '';
                    
                    // Pre-fill labels
                    document.getElementById('province').value = data.province || '';
                    document.getElementById('city').value = data.city || '';
                    document.getElementById('district').value = data.district || '';
                    document.getElementById('village').value = data.village || '';
                    
                    await loadProvinces(data.province_id);
                } else {
                    alert('Sesi tidak valid.');
                }
            } catch (error) {
                console.error(error);
            }
        }

        // Regional Dropdown Logic
        async function loadProvinces(selectedId) {
            const res = await fetch(`${REGIONAL_PROXY}provinces`);
            const data = await res.json();
            const select = document.getElementById('province_id');
            data.sort((a, b) => a.name.localeCompare(b.name)).forEach(p => {
                const opt = new Option(p.name, p.id);
                if (p.id == selectedId) opt.selected = true;
                select.add(opt);
            });
            if (selectedId) select.dispatchEvent(new Event('change'));
        }

        document.getElementById('province_id').onchange = async function() {
            const id = this.value;
            const text = this.options[this.selectedIndex].text;
            document.getElementById('province').value = text;
            
            const citySelect = document.getElementById('city_id');
            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
            citySelect.disabled = !id;
            
            if (id) {
                const res = await fetch(`${REGIONAL_PROXY}regencies/${id}`);
                const data = await res.json();
                data.sort((a,b) => a.name.localeCompare(b.name)).forEach(c => citySelect.add(new Option(c.name, c.id)));
                citySelect.dispatchEvent(new Event('change'));
            }
        };

        document.getElementById('city_id').onchange = async function() {
            const id = this.value;
            const text = this.options[this.selectedIndex].text;
            document.getElementById('city').value = text;
            
            const distSelect = document.getElementById('district_id');
            distSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            distSelect.disabled = !id;
            
            if (id) {
                const res = await fetch(`${REGIONAL_PROXY}districts/${id}`);
                const data = await res.json();
                data.sort((a,b) => a.name.localeCompare(b.name)).forEach(d => distSelect.add(new Option(d.name, d.id)));
                distSelect.dispatchEvent(new Event('change'));
            }
        };

        document.getElementById('district_id').onchange = async function() {
            const id = this.value;
            const text = this.options[this.selectedIndex].text;
            document.getElementById('district').value = text;
            
            const villSelect = document.getElementById('village_id');
            villSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            villSelect.disabled = !id;
            
            if (id) {
                const res = await fetch(`${REGIONAL_PROXY}villages/${id}`);
                const data = await res.json();
                data.sort((a,b) => a.name.localeCompare(b.name)).forEach(v => villSelect.add(new Option(v.name, v.id)));
                villSelect.dispatchEvent(new Event('change'));
            }
        };

        document.getElementById('village_id').onchange = function() {
            document.getElementById('village').value = this.options[this.selectedIndex].text;
        };

        // Form Submission
        document.getElementById('addressForm').onsubmit = async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = 'Sedang Memproses...';

            const formData = new FormData(this);
            const rawBody = Object.fromEntries(formData.entries());

            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(rawBody)
                });
                
                const result = await res.json();
                if (result.status === 'success') {
                    document.getElementById('form-container').style.display = 'none';
                    // Hide Header details but keep bg if needed, or hide whole top part
                    document.getElementById('success-state').style.display = 'block';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert('Gagal: ' + result.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                console.error(error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        };

        window.onload = fetchCustomerData;
    </script>
</body>
</html>

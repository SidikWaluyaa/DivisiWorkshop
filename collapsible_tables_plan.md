# Rencana Implementasi: Tabel Collapsible di Stasiun Kerja
**Stasiun Kerja: Preparation, Production, dan QC**

Dokumen ini berisi panduan dan detail teknis untuk memindahkan visualisasi antrean pengerjaan pada stasiun **Preparation**, **Production**, dan **QC** menjadi berformat **Tabel Collapsible (Accordion Inline)** tanpa mengubah alur data, sistem tabulasi, maupun fungsi Javascript AJAX bawaan.

---

## 📌 Ketentuan Utama (Jangan Diubah)
1. **Sistem Tabulasi Tetap Sama**: 
   - **Preparation**: Tab *Washing*, *Sol*, *Upper*, dan *Review* tidak boleh diubah susunannya.
   - **Production**: Tab *Sol*, *Upper*, *Treatment*, dan *All* tidak boleh diubah susunannya.
   - **QC**: Seluruh sistem tab/antrean yang ada tidak boleh diubah susunannya.
2. **Kesesuaian Data & Logika**: Seluruh variabel data, query filter, penentuan status, dan properti data stasiun tetap sama.
3. **Kompatibilitas Javascript (Zero JS Conflict)**: ID element penting (seperti `id="tech-{{ $type }}-{{ $order->id }}"` dan `id="spk-{{ $order->spk_number }}"`) harus dipertahankan pada tag HTML baru agar AJAX request tetap berjalan normal.

---

## 🛠️ Desain Struktur & Visual Tabel Collapsible

Setiap baris antrean SPK akan diwakili oleh dua baris HTML (`<tr>`):
1. **Baris Ringkasan (Collapsed/Tampilan Utama)**: Setinggi ~60px, memuat data krusial untuk dipantau secara cepat.
2. **Baris Detail (Expanded/Collapsible Row)**: Baris tersembunyi (`colspan`) yang hanya muncul saat baris ringkasan diklik, menggunakan Alpine.js untuk transisi buka/tutup yang mulus.

### 1. Stasiun Persiapan (Preparation)

#### Baris Ringkasan (Collapsed):
- **Kolom 1**: Checkbox multi-select & Nomor Urut Antrean.
- **Kolom 2**: Nomor SPK (Font Mono Tebal) & Badge Prioritas (Urgent/Express dengan efek denyut jika ada).
- **Kolom 3**: Pelanggan & Unit (Nama Customer + Merek/Model Sepatu).
- **Kolom 4**: Tag Layanan (Pembeda warna: Cuci = Teal, Sol = Jingga, Upper = Ungu).
- **Kolom 5**: Penanggung Jawab (Nama teknisi yang di-assign).
- **Kolom 6**: Waktu Masuk / Durasi Antrean.
- **Kolom 7**: Tombol Aksi Expand (`🔽` / `🔼`).

#### Baris Detail (Expanded):
- **Section Kiri (Instruksi & Catatan)**:
  - Kotak peringatan kuning jika ada `technician_notes`.
  - Catatan instruksi awal dari CS (`notes`).
  - Riwayat penanganan keluhan dari tim CX.
- **Section Kanan (Kontrol & Foto)**:
  - Dropdown pemilihan teknisi (mempertahankan `id="tech-{{ $type }}-{{ $order->id }}"`).
  - Tombol kontrol stasiun: `Mulai Pengerjaan` (Start), `Jeda` (Pause), `Selesai` (Finish), dan `Lapor`.
  - Galeri foto sepatu sebelum/sesudah pengerjaan.

---

### 2. Stasiun Produksi (Production)

#### Baris Ringkasan (Collapsed):
- **Kolom 1**: Checkbox & Nomor Urut.
- **Kolom 2**: Nomor SPK & Prioritas.
- **Kolom 3**: Nama Customer & Merek/Warna Sepatu.
- **Kolom 4**: Ringkasan Pengerjaan Divisi (Sol, Upper, Treatment).
- **Kolom 5**: Nama Teknisi Aktif yang ditunjuk.
- **Kolom 6**: Tombol Aksi Expand (`🔽` / `🔼`).

#### Baris Detail (Expanded):
- Kotak dropdown assign teknisi sesuai divisi tugas (Sol, Upper, Treatment).
- Tombol kontrol pengerjaan produksi (`Mulai`, `Selesai`).
- Catatan teknisi dan log pengerjaan sebelumnya.
- Daftar material pendukung yang dipesan ke Gudang.
- Galeri Foto Sepatu.

---

### 3. Stasiun Quality Control (QC)

#### Baris Ringkasan (Collapsed):
- **Kolom 1**: Checkbox & Nomor Urut.
- **Kolom 2**: Nomor SPK.
- **Kolom 3**: Nama Customer & Unit Sepatu.
- **Kolom 4**: Status Pengerjaan QC Divisi (Jahit, Cleanup, Final - berupa centang hijau jika sudah selesai).
- **Kolom 5**: Tombol Aksi Expand (`🔽` / `🔼`).

#### Baris Detail (Expanded):
- Detail pencatatan waktu mulai & selesai beserta durasi (menit) untuk masing-masing tahap QC (Jahit, Cleanup, Final).
- Tombol aksi final: `Approve & Finish` (OK), `Lapor / Follow Up` (Pending), dan `Revisi...` (Kembali ke Produksi).
- Foto hasil akhir sepatu untuk verifikasi visual langsung oleh kepala QC.

---

## 💻 Contoh Struktur Kode Blade & Alpine.js

Implementasi pada loop stasiun dapat menggunakan pola berikut:

```html
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3">No</th>
            <th class="px-4 py-3">SPK</th>
            <th class="px-4 py-3">Pelanggan</th>
            <th class="px-4 py-3">Layanan</th>
            <th class="px-4 py-3">Teknisi</th>
            <th class="px-4 py-3">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
        @foreach($orders as $order)
        <tr x-data="{ expanded: false }" class="hover:bg-gray-50/50 transition-colors">
            
            {{-- Baris Utama --}}
            <td class="px-4 py-4">{{ $loop->iteration }}</td>
            <td class="px-4 py-4 font-mono font-bold">{{ $order->spk_number }}</td>
            <td class="px-4 py-4">
                <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                <div class="text-xs text-gray-500">{{ $order->shoe_brand }}</div>
            </td>
            <td class="px-4 py-4">...Layanan Badge...</td>
            <td class="px-4 py-4">{{ $order->{$techByRelation}->name ?? '-' }}</td>
            
            {{-- Tombol Expand --}}
            <td class="px-4 py-4 text-right">
                <button @click="expanded = !expanded" class="p-1 rounded hover:bg-gray-100">
                    <svg :class="{'rotate-180': expanded}" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </td>
            
            {{-- Baris Detail Collapsible --}}
            <tr x-show="expanded" x-cloak x-transition>
                <td colspan="6" class="bg-gray-50 p-4 border-t border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-bold text-xs text-gray-500 uppercase">Catatan & Instruksi</h4>
                            <p class="text-sm mt-1">{{ $order->notes ?? '-' }}</p>
                        </div>
                        <div>
                            {{-- Dropdown & Button Actions (Mempertahankan ID asli) --}}
                            <select id="tech-{{ $type }}-{{ $order->id }}" class="text-sm border-gray-300 rounded-lg">
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($technicians as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                            <button onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" class="...">Mulai</button>
                        </div>
                    </div>
                </td>
            </tr>
        </tr>
        @endforeach
    </tbody>
</table>
```

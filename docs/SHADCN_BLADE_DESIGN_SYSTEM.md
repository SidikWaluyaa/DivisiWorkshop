# Shoe Workshop — Shadcn-Blade Design System Blueprint (Desember 2026)

Dokumen ini adalah panduan arsitektur resmi untuk pembaruan antarmuka **Sistem Workshop** berbasis **Shadcn UI + Laravel Blade + Alpine.js + Livewire** dengan prinsip **100% Zero Logic Change**.

File preview interaktif dapat diakses kapan saja melalui browser lokal:
- **URL Preview:** `http://sistemworkshop.test/shadcn-preview.html`
- **File Path:** `public/shadcn-preview.html`

---

## 1. Prinsip Utama: 100% Zero Logic Change

1. **Tidak Mengubah Logika Backend / Livewire:**
   - Seluruh *property* Livewire (`wire:model`, `wire:model.live="selectedItems"`, `wire:click`, `$wire.updateTechnicianWithReason(...)`) dipertahankan 100% utuh.
2. **Tidak Mengubah State Alpine.js:**
   - Seluruh state interaktif yang sudah berjalan (seperti `x-data="{ expanded: false, showFinishModal: false, ... }"` dan `@click="expanded = !expanded"` pada `station-card.blade.php`) tetap sama persis.
3. **Murni Lapisan Visual & Responsif:**
   - Pembaruan hanya menyentuh token CSS (`resources/css/app.css`), komponen modular `<x-ui.*>`, dan kelas utilitas responsif Tailwind CSS.

---

## 2. Token Warna & Tipografi Resmi

### A. Brand Palette (Tetap Dipertahankan)
| Token | HEX | Kegunaan |
| :--- | :--- | :--- |
| `--primary` | `#22AF85` | **SW Emerald** — Tombol aksi utama, status aktif, focus ring, progress bar. |
| `--secondary` | `#1A4D3E` | **SW Deep Forest** — Sidebar navigasi, header kartu utama, identitas brand. |
| `--accent` | `#FFC232` | **SW Gold** — Badge prioritas/aksesoris, sorotan cepat, aksen CTA penting. |

### B. Light Mode vs Dark Mode (`Slate Dimmed`)
Dark Mode resmi menggunakan palet **Slate Dimmed** (bukan hitam pekat `#000000` / `#09090B`) untuk mencegah efek *halation* (mata cepat lelah/pusing saat membaca tabel SPK):

| CSS Variable | Light Mode (`:root`) | Dark Mode (`.dark` — Slate Dimmed) | Kegunaan |
| :--- | :--- | :--- | :--- |
| `--background` | `#F8FAFC` (Slate-50) | `#13171F` (Slate Dimmed Surface) | Latar belakang utama halaman |
| `--card` | `#FFFFFF` (Pure White) | `#1B202B` (Elevated Slate Card) | Permukaan kartu, tabel, dan modal |
| `--muted` | `#F1F5F9` (Slate-100) | `#242B38` (Slate Muted/Hover) | Header tabel, latar tab bar, dan hover baris |
| `--border` | `#E2E8F0` (Slate-200) | `#2C3545` (Subtle 1px Slate Border) | Garis batas 1px tegas khas Shadcn |
| `--foreground` | `#0F172A` (Slate-900) | `#E6EDF3` (Soft Off-White) | Teks utama (nyaman dibaca lama) |
| `--muted-foreground` | `#64748B` (Slate-500) | `#94A3B8` (Slate-400) | Teks sekunder, label kolom, dan caption |

### C. Tipografi Resmi (`Inter` + `JetBrains Mono`)
- **Font Utama (`--font-sans`):** `Inter` dengan fitur *optical kerning* (`font-feature-settings: "cv02", "cv03", "cv04", "cv11"`, `letter-spacing: -0.012em` pada body dan `-0.025em` pada heading).
- **Font Monospace (`--font-mono`):** `JetBrains Mono` (`tabular-nums`) untuk seluruh Nomor SPK (`S-2611-...`), nominal Rupiah, hitungan SLA, dan *inline code badge*.

---

## 3. Aturan Standar Mobile vs Desktop

| Komponen / Elemen | Aturan Mobile (`< 768px` / HP Teknisi) | Aturan Desktop (`>= 768px` / PC Admin & CS) | Kelas Tailwind Standar |
| :--- | :--- | :--- | :--- |
| **Container & Overflow** | Terkunci 100% di dalam viewport tanpa menggeser halaman ke kanan | Lebar maksimum `max-w-7xl` | `overflow-x-hidden max-w-[100vw]` pada `html, body` + `min-w-0` pada setiap anak flex/grid |
| **Ukuran Tombol & Input** | Tinggi **`40px` (`h-10`)** agar nyaman disentuh jari | Tinggi **`36px` (`md:h-9`)** yang ringkas dan padat | `h-10 md:h-9 px-3.5 text-xs font-semibold rounded-lg` |
| **Tab Stasiun & Filter Bar** | **Horizontal Swipeable Pills** (geser kiri-kanan halus tanpa scrollbar) | Tampil utuh sejajar dalam satu baris | `overflow-x-auto no-scrollbar max-w-full whitespace-nowrap` |
| **Modal / Dialog (`<x-ui.dialog>`)** | **Bottom Sheet Drawer** (muncul dari bawah layar dengan `rounded-t-2xl`) | **Centered Modal Dialog** di tengah layar (`sm:rounded-xl max-w-md`) | Wrapper: `items-end sm:items-center p-0 sm:p-4`<br/>Panel: `w-full sm:max-w-md rounded-t-2xl sm:rounded-xl` |

---

## 4. Aturan Standar Tabel & `station-card` (Hybrid Responsive Table)

Komponen tabel seperti `resources/views/components/station-card.blade.php` menggunakan pola **Hybrid Responsive Table**:

1. **Di Layar Desktop (`>= 768px`):**
   - Seluruh kolom tampil lengkap (`Checkbox`, `Nomor SPK & Unit`, `Pelanggan`, `PIC & Sub-Stasiun`, `Status`, `Sisa SLA`, dan `Chevron Detail`).
2. **Di Layar Mobile (`< 768px`):**
   - Kolom sekunder diberi class `hidden md:table-cell` agar tabel tidak pernah melebar keluar layar HP.
   - Kolom utama menampilkan **Checkbox + Nomor SPK + Ringkasan 1 Baris (`md:hidden`) + Status + Chevron**.
3. **Expandable Drawer (`@click="expanded = !expanded"` / `<tr x-show="expanded">`):**
   - Saat baris diklik di HP maupun PC, baris drawer di bawahnya terbuka menampilkan seluruh detail kolom sekunder, pilihan teknisi PIC, dan tombol aksi tanpa mengubah logika Alpine/Livewire sedikit pun.


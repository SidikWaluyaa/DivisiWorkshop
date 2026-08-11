<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl text-gray-800 dark:text-white leading-tight flex items-center gap-2">
                <span>🚀</span> Manajemen Pengumuman & Rilis Fitur Baru
            </h2>
            <span class="text-xs bg-teal-100 text-teal-800 font-bold px-3 py-1 rounded-full border border-teal-200">
                In-App Notification & Release Notes
            </span>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="announcementPage()">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold rounded-2xl shadow-sm flex items-center justify-between">
                <span class="flex items-center gap-2">✅ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>
        @endif

        {{-- Form & Live Preview Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Form Column --}}
            <div class="lg:col-span-7 bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl space-y-5">
                
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">📢</span>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 dark:text-white">Buat Pengumuman Rilis</h3>
                            <p class="text-[11px] text-gray-500">Otomatisasi pengisian untuk meminimalkan tindakan manual</p>
                        </div>
                    </div>
                </div>

                {{-- Bar Tombol Otomatisasi 1-Klik --}}
                <div class="p-3 bg-slate-900 rounded-2xl text-white space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-widest text-teal-400">⚡ Opsi Otomatisasi 1-Klik</span>
                        <span class="text-[10px] text-slate-400">Pilih preset untuk auto-fill instant</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button type="button" @click="fetchWorkLog()" 
                                class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold py-2 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-md transition-all">
                            <span>🤖</span>
                            <span>Auto-Fill dari Laporan Kerja</span>
                        </button>

                        <div class="grid grid-cols-3 gap-1">
                            <button type="button" @click="applyPreset('feature')" 
                                    class="bg-slate-800 hover:bg-slate-700 text-teal-300 font-bold py-1.5 px-2 rounded-lg text-[10px] border border-teal-500/30 text-center">
                                ✨ Fitur Baru
                            </button>
                            <button type="button" @click="applyPreset('bugfix')" 
                                    class="bg-slate-800 hover:bg-slate-700 text-rose-300 font-bold py-1.5 px-2 rounded-lg text-[10px] border border-rose-500/30 text-center">
                                🐛 Bug Fix
                            </button>
                            <button type="button" @click="applyPreset('maintenance')" 
                                    class="bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold py-1.5 px-2 rounded-lg text-[10px] border border-amber-500/30 text-center">
                                🔧 Maintenance
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Judul Pengumuman</label>
                            <input type="text" name="title" x-model="createForm.title" required 
                                   placeholder="Contoh: 🚀 Fitur Modern Date Range Picker di Page CX" 
                                   class="w-full rounded-xl border-gray-200 text-xs font-bold text-gray-800 focus:ring-teal-500 py-2.5">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Versi Rilis</label>
                            <input type="text" name="version" x-model="createForm.version" required 
                                   placeholder="v2.4.0" 
                                   class="w-full rounded-xl border-gray-200 text-xs font-bold text-gray-800 focus:ring-teal-500 py-2.5 text-center">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Kategori</label>
                            <select name="category" x-model="createForm.category" required 
                                    class="w-full rounded-xl border-gray-200 text-xs font-bold text-gray-800 focus:ring-teal-500 py-2.5">
                                <option value="FEATURE_UPDATE">✨ Fitur Baru</option>
                                <option value="MAINTENANCE">🔧 Pemeliharaan</option>
                                <option value="SYSTEM_NOTICE">📢 Pengumuman Umum</option>
                                <option value="BUG_FIX">🐛 Perbaikan Bug</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Ringkasan Singkat (Toast Popup)</label>
                            <input type="text" name="summary" x-model="createForm.summary" 
                                   placeholder="1 kalimat singkat penjelasan fitur untuk orang awam..." 
                                   class="w-full rounded-xl border-gray-200 text-xs font-medium text-gray-800 focus:ring-teal-500 py-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Rincian Penjelasan Lengkap (Description)</label>
                        <textarea name="description" x-model="createForm.description" rows="5" required 
                                  placeholder="Tuliskan poin-poin fitur baru..." 
                                  class="w-full rounded-xl border-gray-200 text-xs font-normal text-gray-800 focus:ring-teal-500 py-2.5"></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <button type="button" @click="resetForm()" class="text-xs text-gray-400 hover:text-gray-600 font-bold">
                            🔄 Clear / Reset Form
                        </button>

                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest transition-all shadow-md flex items-center gap-2">
                            <span>🚀</span> Publikasikan Sekarang
                        </button>
                    </div>
                </form>
            </div>

            {{-- Preview Column --}}
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-gray-100 dark:border-gray-700 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2.5">
                        <span class="text-xs font-black uppercase tracking-wider text-gray-800 dark:text-white flex items-center gap-1.5">
                            <span>👁️</span> Live Preview Tampilan User
                        </span>
                        <span class="text-[10px] bg-slate-100 text-slate-700 font-bold px-2 py-0.5 rounded">
                            Real-time
                        </span>
                    </div>

                    {{-- Toast Preview --}}
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">1. Preview Toast Popup (Kanan Bawah)</span>
                        <div class="bg-slate-900 text-white rounded-2xl shadow-xl border border-teal-500/30 p-3.5 text-left">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg flex-shrink-0">
                                        🎉
                                    </div>
                                    <div>
                                        <span class="text-[8px] font-black uppercase tracking-widest text-teal-400" x-text="'Fitur Baru Tersedia (' + (createForm.version || 'v1.0.0') + ')'"></span>
                                        <h4 class="text-xs font-bold text-white leading-tight mt-0.5 line-clamp-1" x-text="createForm.title || 'Judul Pengumuman'"></h4>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-300 mt-2 line-clamp-2 leading-relaxed" x-text="createForm.summary || 'Ringkasan singkat rilis...'"></p>
                        </div>
                    </div>

                    {{-- Modal Preview --}}
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">2. Preview Modal "Apa Yang Baru"</span>
                        <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-sm bg-white">
                            <div class="bg-gradient-to-r from-slate-900 to-teal-950 p-4 text-white">
                                <span class="px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 text-[9px] font-black uppercase tracking-widest border border-teal-500/30"
                                      x-text="(createForm.version || 'v1.0.0') + ' • ' + (createForm.category || 'UPDATE')"></span>
                                <h3 class="text-sm font-black mt-1.5 leading-snug" x-text="createForm.title || 'Judul Pengumuman'"></h3>
                            </div>
                            <div class="p-3 text-[11px] text-gray-700 space-y-2 max-h-40 overflow-y-auto">
                                <div class="p-2 bg-teal-50 border border-teal-100 rounded-xl text-teal-900 font-semibold" x-text="'💡 ' + (createForm.summary || 'Ringkasan singkat...')"></div>
                                <div class="whitespace-pre-line text-[10px] text-gray-600 leading-relaxed" x-text="createForm.description || 'Penjelasan rincian fitur...'"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Pengumuman Terbit --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl">
            <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 dark:text-white mb-4">Daftar Pengumuman Terbit</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-[10px] uppercase tracking-wider text-gray-400 font-black">
                            <th class="py-3 px-4">VERSI / KATEGORI</th>
                            <th class="py-3 px-4">JUDUL & RINGKASAN</th>
                            <th class="py-3 px-4">TANGGAL RILIS</th>
                            <th class="py-3 px-4 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs font-medium">
                        @forelse($announcements as $ann)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750 transition-colors">
                                <td class="py-3.5 px-4 align-top">
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-teal-100 text-teal-800">
                                        {{ $ann->version }} • {{ str_replace('_', ' ', $ann->category) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 align-top">
                                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $ann->title }}</div>
                                    <div class="text-gray-500 text-xs mt-1 line-clamp-2 leading-relaxed">{{ $ann->summary }}</div>
                                </td>
                                <td class="py-3.5 px-4 align-top text-gray-500 text-xs whitespace-nowrap">
                                    {{ $ann->published_at ? $ann->published_at->translatedFormat('d M Y H:i') : '-' }}
                                    <div class="text-[10px] text-gray-400">Oleh: {{ $ann->creator->name ?? 'Admin' }}</div>
                                </td>
                                <td class="py-3.5 px-4 align-top text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" @click="openEditModal({{ json_encode($ann) }})" 
                                                class="text-teal-600 hover:text-teal-800 font-bold text-xs bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-lg border border-teal-200 transition-all flex items-center gap-1">
                                            ✏️ Ubah
                                        </button>
                                        
                                        <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition-all flex items-center gap-1">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400 font-bold">
                                    Belum ada pengumuman yang diterbitkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $announcements->links() }}
            </div>
        </div>

        {{-- Modal Edit Pengumuman --}}
        <template x-if="showEditModal">
            <div class="fixed inset-0 z-[10000] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showEditModal = false"></div>

                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                        <form :action="'{{ url('admin/announcements') }}/' + editForm.id" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="bg-gradient-to-r from-slate-900 to-teal-950 p-6 text-white relative">
                                <button type="button" @click="showEditModal = false" class="absolute top-4 right-4 text-white/60 hover:text-white text-lg">
                                    ✕
                                </button>
                                <span class="px-2.5 py-1 rounded bg-teal-500/20 text-teal-300 text-[10px] font-black uppercase tracking-widest border border-teal-500/30">
                                    Edit Pengumuman Rilis
                                </span>
                                <h2 class="text-xl font-black mt-2 leading-snug" x-text="'Ubah: ' + editForm.title"></h2>
                            </div>

                            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div class="md:col-span-3">
                                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Judul Pengumuman</label>
                                        <input type="text" name="title" x-model="editForm.title" required 
                                               class="w-full rounded-xl border-gray-200 text-xs font-bold text-gray-800 focus:ring-teal-500 py-2.5">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Versi Rilis</label>
                                        <input type="text" name="version" x-model="editForm.version" required 
                                               class="w-full rounded-xl border-gray-200 text-xs font-bold text-gray-800 focus:ring-teal-500 py-2.5 text-center">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Kategori</label>
                                        <select name="category" x-model="editForm.category" required 
                                                class="w-full rounded-xl border-gray-200 text-xs font-bold text-gray-800 focus:ring-teal-500 py-2.5">
                                            <option value="FEATURE_UPDATE">✨ Fitur Baru</option>
                                            <option value="MAINTENANCE">🔧 Pemeliharaan</option>
                                            <option value="SYSTEM_NOTICE">📢 Pengumuman Umum</option>
                                            <option value="BUG_FIX">🐛 Perbaikan Bug</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Ringkasan Singkat (Toast Popup)</label>
                                        <input type="text" name="summary" x-model="editForm.summary" 
                                               class="w-full rounded-xl border-gray-200 text-xs font-medium text-gray-800 focus:ring-teal-500 py-2.5">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Rincian Penjelasan Lengkap (Description)</label>
                                    <textarea name="description" x-model="editForm.description" rows="5" required 
                                              class="w-full rounded-xl border-gray-200 text-xs font-normal text-gray-800 focus:ring-teal-500 py-2.5"></textarea>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-750 px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-2">
                                <button type="button" @click="showEditModal = false" 
                                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-xl text-xs transition-all">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all shadow-md">
                                    💾 Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function announcementPage() {
            return {
                showEditModal: false,
                createForm: {
                    title: '🚀 Pembaruan Fitur & Optimasi Sistem ({{ date('d M Y') }})',
                    version: '{{ $suggestedVersion }}',
                    category: 'FEATURE_UPDATE',
                    summary: 'Pembaruan dan optimasi performa sistem terbaru untuk mempermudah operasional tim.',
                    description: "Penjelasan pembaruan:\n1. ⚡ Optimasi performa dan alur kerja.\n2. 🎨 Penyempurnaan tampilan UI/UX."
                },
                editForm: {
                    id: null,
                    title: '',
                    version: '',
                    category: '',
                    summary: '',
                    description: ''
                },
                openEditModal(announcement) {
                    this.editForm = {
                        id: announcement.id,
                        title: announcement.title || '',
                        version: announcement.version || '',
                        category: announcement.category || 'FEATURE_UPDATE',
                        summary: announcement.summary || '',
                        description: announcement.description || ''
                    };
                    this.showEditModal = true;
                },
                applyPreset(type) {
                    if (type === 'feature') {
                        this.createForm.title = '🚀 Fitur Baru: Pembaruan & Peningkatan Sistem (' + new Date().toLocaleDateString('id-ID') + ')';
                        this.createForm.category = 'FEATURE_UPDATE';
                        this.createForm.summary = 'Penambahan fitur baru untuk mempercepat pengolahan data dan laporan.';
                        this.createForm.description = "Apa saja yang baru:\n1. 🌟 Modul fitur baru siap digunakan.\n2. ⚡ Peningkatan kecepatan respon sistem.\n3. 📊 Laporan data kini lebih akurat.";
                    } else if (type === 'bugfix') {
                        this.createForm.title = '🐛 Perbaikan Bug & Optimasi Performa (' + new Date().toLocaleDateString('id-ID') + ')';
                        this.createForm.category = 'BUG_FIX';
                        this.createForm.summary = 'Perbaikan bug dan penyesuaian stabilitas sistem.';
                        this.createForm.description = "Perbaikan yang dilakukan:\n1. 🛠️ Memperbaiki masalah tampilan dan kendala filter.\n2. ⚡ Pengoptimalan pencarian data.";
                    } else if (type === 'maintenance') {
                        this.createForm.title = '🔧 Pemeliharaan Rutin Server & Stabilitas Sistem (' + new Date().toLocaleDateString('id-ID') + ')';
                        this.createForm.category = 'MAINTENANCE';
                        this.createForm.summary = 'Pembersihan database dan pemeliharaan performa server.';
                        this.createForm.description = "Rincian pemeliharaan:\n1. 🧹 Optimasi database dan pembersihan berkas sementara.\n2. 🔒 Peningkatan keamanan akses data.";
                    }
                },
                async fetchWorkLog() {
                    try {
                        const res = await fetch('{{ route('admin.announcements.fetch-work-log') }}');
                        const json = await res.json();
                        if (json.success && json.data) {
                            this.createForm.title = json.data.title;
                            this.createForm.category = json.data.category;
                            this.createForm.summary = json.data.summary;
                            this.createForm.description = json.data.description;
                            alert('✅ Berhasil membaca data dari berkas ' + json.data.filename + '!');
                        } else {
                            alert(json.message || 'Gagal membaca laporan kerja.');
                        }
                    } catch (e) {
                        alert('Gagal mengambil data dari laporan kerja harian.');
                    }
                },
                resetForm() {
                    this.createForm.title = '';
                    this.createForm.version = '{{ $suggestedVersion }}';
                    this.createForm.category = 'FEATURE_UPDATE';
                    this.createForm.summary = '';
                    this.createForm.description = '';
                }
            }
        }
    </script>
</x-app-layout>

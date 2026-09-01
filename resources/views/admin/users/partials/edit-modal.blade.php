<x-modal name="edit-user-modal-{{ $user->id }}" :show="$errors->any() && old('form_type') === 'edit_user_' . $user->id" focusable maxWidth="4xl">
    @php
        $isThisFormFailed = old('form_type') === 'edit_user_' . $user->id;
        $currentName = $isThisFormFailed ? old('name', $user->name) : $user->name;
        $currentEmail = $isThisFormFailed ? old('email', $user->email) : $user->email;
        $currentPhone = $isThisFormFailed ? old('phone', $user->phone) : $user->phone;
        $currentRole = $isThisFormFailed ? old('role', $user->role) : $user->role;
        $currentActive = $isThisFormFailed ? old('is_active', $user->is_active ? '1' : '0') : ($user->is_active ? '1' : '0');
        $currentSpec = $isThisFormFailed ? old('specialization', $user->specialization) : $user->specialization;

        // Decoded Access Rights
        $dbAccess = $user->access_rights ?? [];
        if (is_string($dbAccess)) {
            $dbAccess = json_decode($dbAccess, true) ?? [];
        }
        $currentAccess = $isThisFormFailed ? old('access_rights', $dbAccess) : $dbAccess;
        if (is_string($currentAccess)) {
            $currentAccess = json_decode($currentAccess, true) ?? [];
        }
    @endphp

    <div x-data="{
        activeTab: 'personal',
        localRole: '{{ $currentRole }}',
        isActive: {{ $currentActive == '1' ? 'true' : 'false' }},
        searchQuery: '',
        selectedAccess: {{ json_encode(array_values((array) $currentAccess)) }},

        applyPreset(roleType) {
            const presets = {
                user: [],
                technician: [],
                pic: [],
                gudang: ['gudang', 'warehouse.storage', 'manifest.index', 'admin.materials.request'],
                cs: ['cs', 'cs.greeting', 'cs.spk', 'dashboard'],
                finance: ['finance', 'manifest.index'],
                spv: ['dashboard', 'workshop.dashboard', 'admin.performance'],
                hr: ['admin.users', 'admin.reports'],
                admin: {{ json_encode(collect($allDivisions)->pluck('modules')->flatMap(fn($m) => array_keys($m))->values()) }},
                owner: {{ json_encode(collect($allDivisions)->pluck('modules')->flatMap(fn($m) => array_keys($m))->values()) }}
            };
            this.selectedAccess = presets[roleType] || [];
        },
        countActiveInGroup(modules) {
            return Object.keys(modules).filter(key => this.selectedAccess.includes(key)).length;
        }
    }" class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 font-sans">

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-0 m-0" onsubmit="let btn = this.querySelector('button[type=submit]'); setTimeout(() => btn.disabled = true, 0); btn.querySelector('.submit-spinner').classList.remove('hidden'); btn.querySelector('.submit-text').innerText = '{{ __('Menyimpan...') }}';">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_type" value="edit_user_{{ $user->id }}">

            {{-- Modal Header --}}
            <div class="p-6 sm:p-7 bg-gradient-to-r from-[#1a3b34] via-[#22AF85] to-[#1a3b34] text-white flex justify-between items-center relative overflow-hidden border-b border-emerald-400/20">
                <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                <div class="relative z-10 flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md flex items-center justify-center text-[#FFC232] shadow-sm shrink-0 font-black text-sm">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-white/10 rounded-full text-[9px] font-black uppercase tracking-widest text-[#FFC232]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232] animate-pulse"></span>
                            ACCESS CONTROL ENGINE
                        </div>
                        <h2 class="text-xl font-black text-white tracking-tight leading-tight">Edit Akses User: <span class="text-[#FFC232]">{{ $user->name }}</span></h2>
                    </div>
                </div>

                <button type="button" x-on:click="$dispatch('close')" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition-all active:scale-95 shrink-0">
                    ✕
                </button>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex border-b border-slate-100 bg-slate-50/80 px-6 pt-3 gap-2 font-sans">
                <button type="button"
                    @click="activeTab = 'personal'"
                    :class="activeTab === 'personal' ? 'bg-white text-[#22AF85] font-black border-t-2 border-[#22AF85] shadow-xs rounded-t-2xl' : 'text-slate-500 hover:text-slate-800 font-bold border-t-2 border-transparent'"
                    class="py-3 px-5 text-xs transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Profil &amp; Keamanan</span>
                </button>
                <button type="button"
                    @click="activeTab = 'access'"
                    :class="activeTab === 'access' ? 'bg-white text-[#22AF85] font-black border-t-2 border-[#22AF85] shadow-xs rounded-t-2xl' : 'text-slate-500 hover:text-slate-800 font-bold border-t-2 border-transparent'"
                    class="py-3 px-5 text-xs transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    <span>Hak Akses Modul</span>
                    <span class="px-2 py-0.5 text-[10px] bg-emerald-100 text-emerald-800 rounded-full font-black" x-text="selectedAccess.length"></span>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 max-h-[65vh] overflow-y-auto font-sans space-y-6">

                {{-- TAB 1: PROFIL & KEAMANAN --}}
                <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left: Data Personal --}}
                        <div class="space-y-4 bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                            <h3 class="text-xs font-black text-[#22AF85] uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span> Data Personal
                            </h3>
                            <div>
                                <x-input-label for="name_{{ $user->id }}" :value="__('Nama Lengkap')" class="font-bold text-xs text-slate-700" />
                                <x-text-input id="name_{{ $user->id }}" class="block mt-1.5 w-full bg-white border border-slate-200 rounded-xl text-xs font-bold focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 py-2.5" type="text" name="name" :value="$currentName" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email_{{ $user->id }}" :value="__('Alamat Email')" class="font-bold text-xs text-slate-700" />
                                <x-text-input id="email_{{ $user->id }}" class="block mt-1.5 w-full bg-white border border-slate-200 rounded-xl text-xs font-bold focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 py-2.5" type="email" name="email" :value="$currentEmail" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone_{{ $user->id }}" :value="__('No. WhatsApp')" class="font-bold text-xs text-slate-700" />
                                <x-text-input id="phone_{{ $user->id }}" class="block mt-1.5 w-full bg-white border border-slate-200 rounded-xl text-xs font-bold focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 py-2.5" type="text" name="phone" :value="$currentPhone" placeholder="628123456789" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Right: Peran & Keamanan --}}
                        <div class="space-y-4 bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                            <h3 class="text-xs font-black text-[#22AF85] uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span> Peran &amp; Keamanan
                            </h3>
                            <div>
                                <x-input-label for="role_{{ $user->id }}" :value="__('Role Akun')" class="font-bold text-xs text-slate-700 mb-1.5" />
                                <select id="role_{{ $user->id }}" name="role" x-model="localRole" class="block w-full border border-slate-200 bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 rounded-xl text-xs font-bold py-2.5 cursor-pointer">
                                    <option value="user" {{ $currentRole === 'user' ? 'selected' : '' }}>User Staff</option>
                                    <option value="technician" {{ $currentRole === 'technician' ? 'selected' : '' }}>Technician / Teknisi</option>
                                    <option value="pic" {{ $currentRole === 'pic' ? 'selected' : '' }}>PIC Material</option>
                                    <option value="gudang" {{ $currentRole === 'gudang' ? 'selected' : '' }}>Staff Gudang</option>
                                    <option value="cs" {{ $currentRole === 'cs' ? 'selected' : '' }}>Customer Service</option>
                                    <option value="finance" {{ $currentRole === 'finance' ? 'selected' : '' }}>Finance / Kasir</option>
                                    <option value="spv" {{ $currentRole === 'spv' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="hr" {{ $currentRole === 'hr' ? 'selected' : '' }}>HR / HRD</option>
                                    @if(in_array(auth()->user()->role, ['admin', 'owner']))
                                    <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Administrator</option>
                                        @if(auth()->user()->email === 'admin@workshop.com')
                                        <option value="owner" {{ $currentRole === 'owner' ? 'selected' : '' }}>Owner / Direktur</option>
                                        @else
                                            @if($currentRole === 'owner')
                                            <option value="owner" selected disabled>Owner / Direktur (Terkunci)</option>
                                            @else
                                            <option value="owner" disabled>Owner / Direktur (Hanya admin@workshop.com)</option>
                                            @endif
                                        @endif
                                    @endif
                                </select>
                            </div>

                            {{-- Status Aktif Toggle Switch --}}
                            <div>
                                <x-input-label :value="__('Status Akun')" class="font-bold text-xs text-slate-700 mb-2" />
                                <input type="hidden" name="is_active" :value="isActive ? '1' : '0'">
                                <label for="is_active_toggle_{{ $user->id }}" class="flex items-center cursor-pointer select-none">
                                    <div class="relative">
                                        <input type="checkbox" id="is_active_toggle_{{ $user->id }}"
                                               x-model="isActive"
                                               class="sr-only peer">
                                        <div class="w-14 h-8 bg-slate-200 rounded-full peer-checked:bg-[#22AF85] transition-colors duration-300 shadow-inner"></div>
                                        <div class="absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 peer-checked:translate-x-6 shadow-md flex items-center justify-center">
                                            <svg x-show="isActive" class="w-3.5 h-3.5 text-[#22AF85]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            <svg x-show="!isActive" class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-xs font-black transition-colors duration-300" :class="isActive ? 'text-emerald-700' : 'text-rose-600'">
                                        <span x-show="isActive">● Aktif</span>
                                        <span x-show="!isActive">○ Nonaktif</span>
                                    </span>
                                </label>
                                <p class="text-[11px] mt-1.5 transition-colors font-medium" :class="isActive ? 'text-slate-400' : 'text-rose-500'">
                                    <span x-show="isActive">Pengguna dapat login dan mengakses sistem.</span>
                                    <span x-show="!isActive">Pengguna akan langsung ter-logout dan tidak bisa login.</span>
                                </p>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>

                            {{-- Specialization (Technician only) --}}
                            <div x-show="localRole === 'technician'" x-transition x-cloak>
                                <x-input-label for="specialization_{{ $user->id }}" :value="__('Spesialisasi Teknis')" class="font-bold text-xs text-slate-700 mb-1.5" />
                                <select id="specialization_{{ $user->id }}" name="specialization" class="block w-full border border-slate-200 bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 rounded-xl text-xs font-bold py-2.5 cursor-pointer">
                                    <option value="">-- Pilih Spesialisasi --</option>
                                    <optgroup label="1. Preparation (Persiapan)">
                                        <option value="Washing" {{ in_array($currentSpec, ['Washing', 'Persiapan (Cuci)']) ? 'selected' : '' }}>Washing (Cuci)</option>
                                        <option value="Bongkar Sol" {{ in_array($currentSpec, ['Bongkar Sol', 'Prep Sol']) ? 'selected' : '' }}>Bongkar Sol</option>
                                        <option value="Bongkar Upper" {{ in_array($currentSpec, ['Bongkar Upper', 'Prep Upper']) ? 'selected' : '' }}>Bongkar Upper</option>
                                    </optgroup>
                                    <optgroup label="2. Production (Produksi / Reparasi)">
                                        <option value="Reparasi Sol" {{ in_array($currentSpec, ['Reparasi Sol', 'Sol Repair']) ? 'selected' : '' }}>Reparasi Sol</option>
                                        <option value="Reparasi Upper" {{ in_array($currentSpec, ['Reparasi Upper', 'Upper Repair']) ? 'selected' : '' }}>Reparasi Upper</option>
                                        <option value="Reparasi Treatment" {{ in_array($currentSpec, ['Reparasi Treatment', 'Treatment', 'Repaint']) ? 'selected' : '' }}>Reparasi Treatment / Cleaning</option>
                                    </optgroup>
                                    <optgroup label="3. Quality Control (QC)">
                                        <option value="QC Jahit" {{ in_array($currentSpec, ['QC Jahit', 'Jahit']) ? 'selected' : '' }}>QC Jahit</option>
                                        <option value="QC Cleanup" {{ in_array($currentSpec, ['QC Cleanup', 'Clean Up']) ? 'selected' : '' }}>QC Cleanup</option>
                                        <option value="QC Final" {{ in_array($currentSpec, ['QC Final', 'PIC QC']) ? 'selected' : '' }}>QC Final</option>
                                    </optgroup>
                                </select>
                                <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- Password Section (Optional on Edit) --}}
                    <div class="bg-amber-50/50 p-5 rounded-2xl border border-amber-200/60">
                        <h4 class="text-xs font-black text-amber-800 uppercase tracking-wider flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Ganti Password (Opsional)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="password_{{ $user->id }}" :value="__('Password Baru')" class="text-xs font-bold text-slate-700" />
                                <x-text-input id="password_{{ $user->id }}" class="block mt-1.5 w-full text-xs font-bold border-slate-200 rounded-xl bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 py-2.5" type="password" name="password" placeholder="Kosongkan jika tidak diganti" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation_{{ $user->id }}" :value="__('Konfirmasi Password')" class="text-xs font-bold text-slate-700" />
                                <x-text-input id="password_confirmation_{{ $user->id }}" class="block mt-1.5 w-full text-xs font-bold border-slate-200 rounded-xl bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 py-2.5" type="password" name="password_confirmation" placeholder="Ketik ulang password baru" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: HAK AKSES MODUL --}}
                <div x-show="activeTab === 'access'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-5" x-cloak>

                    {{-- Toolbar: Presets & Search --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/60 p-4 rounded-2xl border border-slate-100">
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Quick Presets:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="applyPreset('cs')" class="px-2.5 py-1 text-[11px] font-black bg-white border border-slate-200 text-pink-600 hover:bg-pink-50 rounded-lg transition-all shadow-xs">CS</button>
                                <button type="button" @click="applyPreset('gudang')" class="px-2.5 py-1 text-[11px] font-black bg-white border border-slate-200 text-amber-600 hover:bg-amber-50 rounded-lg transition-all shadow-xs">Gudang</button>
                                <button type="button" @click="applyPreset('finance')" class="px-2.5 py-1 text-[11px] font-black bg-white border border-slate-200 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all shadow-xs">Finance</button>
                                <button type="button" @click="applyPreset('hr')" class="px-2.5 py-1 text-[11px] font-black bg-white border border-slate-200 text-rose-600 hover:bg-rose-50 rounded-lg transition-all shadow-xs">HRD</button>
                                <button type="button" @click="applyPreset('admin')" class="px-2.5 py-1 text-[11px] font-black bg-[#22AF85] text-white hover:bg-emerald-600 rounded-lg transition-all shadow-xs">Semua</button>
                                <button type="button" @click="selectedAccess = []" class="px-2.5 py-1 text-[11px] font-black bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded-lg transition-all shadow-xs">Reset</button>
                            </div>
                        </div>

                        <div class="w-full md:w-60">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" x-model="searchQuery" placeholder="Cari modul..." class="w-full pl-9 pr-3 py-2 text-xs font-bold rounded-xl border border-slate-200 focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 bg-white">
                            </div>
                        </div>
                    </div>

                    {{-- Accordion Groups --}}
                    <div class="space-y-3">
                        @foreach($allDivisions as $divIndex => $division)
                        @php
                            $jsLabels = json_encode(array_map(fn($l) => strtolower($l), array_values($division['modules'])));
                        @endphp

                        <div x-data="{ open: true }"
                             x-show="searchQuery === '' || {{ $jsLabels }}.some(l => l.includes(searchQuery.toLowerCase()))"
                             class="border border-slate-150 rounded-2xl overflow-hidden shadow-xs bg-white"
                             x-cloak>

                            {{-- Accordion Header --}}
                            <button type="button" @click="open = !open" class="flex justify-between items-center w-full px-4 py-3 bg-slate-50/80 hover:bg-slate-100/60 transition-colors text-left">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $division['color'] }}-400"></span>
                                    {{ $division['title'] }}
                                </h4>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-{{ $division['color'] }}-100 text-{{ $division['color'] }}-700"
                                          x-text="countActiveInGroup({{ json_encode($division['modules']) }}) + '/' + {{ count($division['modules']) }}">
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>

                            {{-- Accordion Body --}}
                            <div x-show="open" x-collapse>
                                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    @foreach($division['modules'] as $key => $label)
                                    <label x-show="searchQuery === '' || '{{ strtolower($label) }}'.includes(searchQuery.toLowerCase())"
                                           class="group relative cursor-pointer select-none"
                                           x-transition>
                                        <input type="checkbox" name="access_rights[]" value="{{ $key }}"
                                               x-model="selectedAccess"
                                               class="peer sr-only">
                                        <div class="p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition-all duration-200 peer-checked:border-{{ $division['color'] }}-500 peer-checked:ring-1 peer-checked:ring-{{ $division['color'] }}-500 peer-checked:bg-{{ $division['color'] }}-50/50 hover:shadow-xs">
                                            <div class="flex items-center gap-3">
                                                <div class="w-5 h-5 rounded-md border-2 border-slate-300 flex items-center justify-center transition-all peer-checked:bg-{{ $division['color'] }}-500 peer-checked:border-{{ $division['color'] }}-500">
                                                    <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <span class="text-xs font-bold text-slate-700 peer-checked:text-{{ $division['color'] }}-700 select-none">{{ $label }}</span>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Admin notice --}}
                    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-start gap-3">
                        <svg class="w-5 h-5 text-[#22AF85] mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div>
                            <h5 class="text-xs font-black text-emerald-900 uppercase tracking-wider">Catatan Administrator</h5>
                            <p class="text-xs font-medium text-emerald-700 mt-0.5">
                                Akun dengan role <strong>Admin / Owner</strong> secara otomatis memiliki akses penuh ke semua modul, terlepas dari pilihan di atas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-5 sm:p-6 bg-slate-50/80 border-t border-slate-100 flex justify-end gap-3 font-sans">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black rounded-xl transition-all active:scale-95">
                    {{ __('Batal') }}
                </button>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#FFC232] hover:bg-amber-400 text-slate-950 text-xs font-black rounded-2xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all border border-amber-300 flex items-center gap-2 cursor-pointer">
                    <svg class="submit-spinner hidden animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="submit-text">{{ __('Simpan Perubahan') }}</span>
                </button>
            </div>
        </form>
    </div>
</x-modal>

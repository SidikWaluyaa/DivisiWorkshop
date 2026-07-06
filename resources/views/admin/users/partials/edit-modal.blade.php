<x-modal name="edit-user-modal-{{ $user->id }}" :show="$errors->any() && old('form_type') === 'edit_user_' . $user->id" focusable maxWidth="4xl">
    @php
        $isThisFormFailed = old('form_type') === 'edit_user_' . $user->id;
        $currentName = $isThisFormFailed ? old('name', $user->name) : $user->name;
        $currentEmail = $isThisFormFailed ? old('email', $user->email) : $user->email;
        $currentPhone = $isThisFormFailed ? old('phone', $user->phone) : $user->phone;
        $currentRole = $isThisFormFailed ? old('role', $user->role) : $user->role;
        $currentActive = $isThisFormFailed ? old('is_active', $user->is_active) : $user->is_active;
        $currentSpec = $isThisFormFailed ? old('specialization', $user->specialization) : $user->specialization;
        $currentAccess = $isThisFormFailed ? old('access_rights', $user->access_rights ?? []) : ($user->access_rights ?? []);
        if (is_string($currentAccess)) {
            $currentAccess = json_decode($currentAccess, true) ?? [];
        }
    @endphp

    <div x-data="{
        activeTab: 'personal',
        localRole: '{{ $currentRole }}',
        isActive: {{ $currentActive ? 'true' : 'false' }},
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
    }" class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-2xl">

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-0 m-0" onsubmit="let btn = this.querySelector('button[type=submit]'); setTimeout(() => btn.disabled = true, 0); btn.querySelector('.submit-spinner').classList.remove('hidden'); btn.querySelector('.submit-text').innerText = '{{ __('Menyimpan...') }}';">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_type" value="edit_user_{{ $user->id }}">

            {{-- Modal Header --}}
            <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    Edit User Access
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-white/70 hover:text-white transition-colors p-1.5 hover:bg-white/10 rounded-full">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 px-6 gap-1">
                <button type="button"
                    @click="activeTab = 'personal'"
                    :class="activeTab === 'personal' ? 'border-teal-500 text-teal-600 dark:text-teal-400 font-bold bg-white dark:bg-gray-900 rounded-t-xl shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil & Keamanan
                </button>
                <button type="button"
                    @click="activeTab = 'access'"
                    :class="activeTab === 'access' ? 'border-teal-500 text-teal-600 dark:text-teal-400 font-bold bg-white dark:bg-gray-900 rounded-t-xl shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    Hak Akses Modul
                    <span class="ml-1 px-2 py-0.5 text-[10px] bg-teal-100 text-teal-800 rounded-full font-bold" x-text="selectedAccess.length"></span>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 max-h-[65vh] overflow-y-auto">

                {{-- TAB 1: PROFIL & KEAMANAN --}}
                <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left: Data Personal --}}
                        <div class="space-y-4 bg-gray-50 dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <h3 class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-1.5 h-3 bg-teal-500 rounded-full"></span> Data Personal
                            </h3>
                            <div>
                                <x-input-label for="name_{{ $user->id }}" :value="__('Nama Lengkap')" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <x-text-input id="name_{{ $user->id }}" class="block mt-1.5 w-full bg-white dark:bg-gray-900 border-gray-200 rounded-xl" type="text" name="name" :value="$currentName" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email_{{ $user->id }}" :value="__('Alamat Email')" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <x-text-input id="email_{{ $user->id }}" class="block mt-1.5 w-full bg-white dark:bg-gray-900 border-gray-200 rounded-xl" type="email" name="email" :value="$currentEmail" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone_{{ $user->id }}" :value="__('No. WhatsApp')" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <x-text-input id="phone_{{ $user->id }}" class="block mt-1.5 w-full bg-white dark:bg-gray-900 border-gray-200 rounded-xl" type="text" name="phone" :value="$currentPhone" placeholder="628xxx" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Right: Peran & Keamanan --}}
                        <div class="space-y-4 bg-gray-50 dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <h3 class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-1.5 h-3 bg-teal-500 rounded-full"></span> Peran & Keamanan
                            </h3>
                            <div>
                                <x-input-label for="role_{{ $user->id }}" :value="__('Role Akun')" class="font-semibold text-gray-700 dark:text-gray-300 mb-1.5" />
                                <select id="role_{{ $user->id }}" name="role" x-model="localRole" class="block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 focus:ring focus:ring-teal-200 rounded-xl shadow-sm text-sm">
                                    <option value="user" {{ $currentRole === 'user' ? 'selected' : '' }}>User Staff</option>
                                    <option value="technician" {{ $currentRole === 'technician' ? 'selected' : '' }}>Technician</option>
                                    <option value="pic" {{ $currentRole === 'pic' ? 'selected' : '' }}>PIC Material</option>
                                    <option value="gudang" {{ $currentRole === 'gudang' ? 'selected' : '' }}>Staff Gudang</option>
                                    <option value="cs" {{ $currentRole === 'cs' ? 'selected' : '' }}>Customer Service</option>
                                    <option value="finance" {{ $currentRole === 'finance' ? 'selected' : '' }}>Finance / Kasir</option>
                                    <option value="spv" {{ $currentRole === 'spv' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="hr" {{ $currentRole === 'hr' ? 'selected' : '' }}>HR / HRD</option>
                                    @if(in_array(auth()->user()->role, ['admin', 'owner']))
                                    <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="owner" {{ $currentRole === 'owner' ? 'selected' : '' }}>Owner / Direktur</option>
                                    @endif
                                </select>
                            </div>

                            {{-- Status Aktif Toggle Switch --}}
                            <div>
                                <x-input-label :value="__('Status Akun')" class="font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                                <input type="hidden" name="is_active" :value="isActive ? '1' : '0'">
                                <label for="is_active_toggle_{{ $user->id }}" class="flex items-center cursor-pointer select-none">
                                    <div class="relative">
                                        <input type="checkbox" id="is_active_toggle_{{ $user->id }}"
                                               x-model="isActive"
                                               class="sr-only peer">
                                        <div class="w-14 h-8 bg-gray-300 dark:bg-gray-700 rounded-full peer-checked:bg-emerald-500 transition-colors duration-300 shadow-inner"></div>
                                        <div class="absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 peer-checked:translate-x-6 shadow-md flex items-center justify-center">
                                            <svg x-show="isActive" class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            <svg x-show="!isActive" class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-sm font-bold transition-colors duration-300" :class="isActive ? 'text-emerald-600' : 'text-red-500'">
                                        <span x-show="isActive">Aktif</span>
                                        <span x-show="!isActive">Nonaktif</span>
                                    </span>
                                </label>
                                <p class="text-[11px] mt-1.5 transition-colors" :class="isActive ? 'text-gray-400' : 'text-red-400'">
                                    <span x-show="isActive">Pengguna dapat login dan mengakses sistem.</span>
                                    <span x-show="!isActive">Pengguna akan langsung ter-logout dan tidak bisa login.</span>
                                </p>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>

                            {{-- Specialization (Technician only) --}}
                            <div x-show="localRole === 'technician'" x-transition x-cloak>
                                <x-input-label for="specialization_{{ $user->id }}" :value="__('Spesialisasi Teknis')" class="font-semibold text-gray-700 dark:text-gray-300 mb-1.5" />
                                <select id="specialization_{{ $user->id }}" name="specialization" class="block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-xl shadow-sm text-sm">
                                    <option value="">-- Pilih Spesialisasi --</option>
                                    <optgroup label="Preparation">
                                        <option value="Washing" {{ $currentSpec === 'Washing' ? 'selected' : '' }}>Washing</option>
                                        <option value="Sol Repair" {{ $currentSpec === 'Sol Repair' ? 'selected' : '' }}>Sol Repair</option>
                                        <option value="Upper Repair" {{ $currentSpec === 'Upper Repair' ? 'selected' : '' }}>Upper Repair</option>
                                    </optgroup>
                                    <optgroup label="Repaint & Treatment">
                                        <option value="Repaint" {{ $currentSpec === 'Repaint' ? 'selected' : '' }}>Repaint</option>
                                        <option value="Treatment" {{ $currentSpec === 'Treatment' ? 'selected' : '' }}>Treatment</option>
                                    </optgroup>
                                    <optgroup label="QC">
                                        <option value="Jahit" {{ $currentSpec === 'Jahit' ? 'selected' : '' }}>Jahit</option>
                                        <option value="Clean Up" {{ $currentSpec === 'Clean Up' ? 'selected' : '' }}>Clean Up</option>
                                        <option value="PIC QC" {{ $currentSpec === 'PIC QC' ? 'selected' : '' }}>PIC QC</option>
                                    </optgroup>
                                </select>
                                <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- Password Section --}}
                    <div class="bg-amber-50/50 dark:bg-amber-950/10 p-5 rounded-2xl border border-amber-100/60 dark:border-amber-900/20">
                        <h4 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-2 mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Ganti Password (Opsional)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="password_{{ $user->id }}" :value="__('Password Baru')" class="text-sm font-semibold text-gray-600" />
                                <x-text-input id="password_{{ $user->id }}" class="block mt-1.5 w-full text-sm border-gray-200 rounded-xl bg-white dark:bg-gray-900" type="password" name="password" placeholder="Kosongkan jika tidak diganti" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation_{{ $user->id }}" :value="__('Konfirmasi Password')" class="text-sm font-semibold text-gray-600" />
                                <x-text-input id="password_confirmation_{{ $user->id }}" class="block mt-1.5 w-full text-sm border-gray-200 rounded-xl bg-white dark:bg-gray-900" type="password" name="password_confirmation" placeholder="Ketik ulang password baru" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: HAK AKSES MODUL --}}
                <div x-show="activeTab === 'access'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-5" x-cloak>

                    {{-- Toolbar: Presets & Search --}}
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 bg-gray-50 dark:bg-gray-800/40 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Quick Presets</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="applyPreset('cs')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-pink-600 hover:bg-pink-50 rounded-lg transition-colors shadow-sm">CS</button>
                                <button type="button" @click="applyPreset('gudang')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors shadow-sm">Gudang</button>
                                <button type="button" @click="applyPreset('finance')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors shadow-sm">Finance</button>
                                <button type="button" @click="applyPreset('hr')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors shadow-sm">HRD</button>
                                <button type="button" @click="applyPreset('admin')" class="px-2.5 py-1 text-[11px] font-bold bg-teal-500 text-white hover:bg-teal-600 rounded-lg transition-colors shadow-sm">Semua</button>
                                <button type="button" @click="selectedAccess = []" class="px-2.5 py-1 text-[11px] font-semibold bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 rounded-lg transition-colors shadow-sm">Reset</button>
                            </div>
                        </div>

                        <div class="w-full md:w-56">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" x-model="searchQuery" placeholder="Cari modul..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-200 bg-white dark:bg-gray-950">
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
                             class="border border-gray-150 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm bg-white dark:bg-gray-900"
                             x-cloak>

                            {{-- Accordion Header --}}
                            <button type="button" @click="open = !open" class="flex justify-between items-center w-full px-4 py-3 bg-gray-50/80 dark:bg-gray-850 hover:bg-gray-100/50 transition-colors text-left">
                                <h4 class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $division['color'] }}-400"></span>
                                    {{ $division['title'] }}
                                </h4>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-{{ $division['color'] }}-100 text-{{ $division['color'] }}-700"
                                          x-text="countActiveInGroup({{ json_encode($division['modules']) }}) + '/' + {{ count($division['modules']) }}">
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
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
                                        <div class="p-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 dark:bg-gray-950 dark:border-gray-800 transition-all duration-200 peer-checked:border-{{ $division['color'] }}-500 peer-checked:ring-1 peer-checked:ring-{{ $division['color'] }}-500 peer-checked:bg-{{ $division['color'] }}-50/50 dark:peer-checked:bg-{{ $division['color'] }}-900/20 hover:shadow-sm">
                                            <div class="flex items-center gap-3">
                                                <div class="w-5 h-5 rounded-md border-2 border-gray-300 dark:border-gray-700 flex items-center justify-center transition-all peer-checked:bg-{{ $division['color'] }}-500 peer-checked:border-{{ $division['color'] }}-500">
                                                    <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 peer-checked:text-{{ $division['color'] }}-700 dark:peer-checked:text-{{ $division['color'] }}-400 select-none">{{ $label }}</span>
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
                    <div class="p-4 bg-teal-50 dark:bg-teal-900/20 rounded-2xl border border-teal-100/50 dark:border-teal-900/20 flex items-start gap-3">
                        <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div>
                            <h5 class="text-sm font-bold text-teal-800 dark:text-teal-300">Catatan Administrator</h5>
                            <p class="text-xs text-teal-600 dark:text-teal-400 mt-1">
                                Akun dengan role <strong>Admin / Owner</strong> secara otomatis memiliki akses penuh ke semua modul, terlepas dari pilihan di atas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all shadow-sm">
                    {{ __('Batal') }}
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white text-sm font-bold rounded-xl hover:from-teal-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-md shadow-teal-500/20 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <svg class="submit-spinner hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="submit-text">{{ __('Simpan Perubahan') }}</span>
                </button>
            </div>
        </form>
    </div>
</x-modal>

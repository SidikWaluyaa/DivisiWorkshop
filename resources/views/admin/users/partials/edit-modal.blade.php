<x-modal name="edit-user-modal-{{ $user->id }}" :show="$errors->any() && old('form_type') === 'edit_user_' . $user->id" focusable maxWidth="4xl">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-0" x-data="{ role: '{{ $user->role }}' }" onsubmit="let btn = this.querySelector('button[type=submit]'); setTimeout(() => btn.disabled = true, 0); btn.querySelector('.submit-spinner').classList.remove('hidden'); btn.querySelector('.submit-text').innerText = '{{ __('Menyimpan...') }}';">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_type" value="edit_user_{{ $user->id }}">

        <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-t-lg">
            <h2 class="text-xl font-bold flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                Edit User Access
            </h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-white/70 hover:text-white transition-colors p-1 hover:bg-white/10 rounded-full">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            {{-- Left Column: User Details --}}
            <div class="lg:col-span-4 p-6 bg-gray-50 dark:bg-gray-800/50 border-r border-gray-100 dark:border-gray-700 space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 border-b pb-2 border-gray-200">Data Personal</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name_{{ $user->id }}" :value="__('Nama Lengkap')" />
                            <x-text-input id="name_{{ $user->id }}" class="block mt-1 w-full bg-white dark:bg-gray-900" type="text" name="name" :value="$user->name" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email_{{ $user->id }}" :value="__('Alamat Email')" />
                            <x-text-input id="email_{{ $user->id }}" class="block mt-1 w-full bg-white dark:bg-gray-900" type="email" name="email" :value="$user->email" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone_{{ $user->id }}" :value="__('No. WhatsApp')" />
                            <x-text-input id="phone_{{ $user->id }}" class="block mt-1 w-full bg-white dark:bg-gray-900" type="text" name="phone" :value="$user->phone" placeholder="628xxx" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 border-b pb-2 border-gray-200 pt-2">Peran & Keamanan</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="role_{{ $user->id }}" :value="__('Role Akun')" class="mb-1" />
                            <select id="role_{{ $user->id }}" name="role" x-model="role" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-lg shadow-sm text-sm">
                                <option value="user">User Staff</option>
                                <option value="technician">Technician</option>
                                <option value="pic">PIC Material</option>
                                <option value="gudang">Staff Gudang</option>
                                <option value="cs">Customer Service</option>
                                <option value="finance">Finance / Kasir</option>
                                <option value="spv">Supervisor</option>
                                <option value="hr">HR / HRD</option>
                                @if(in_array(auth()->user()->role, ['admin', 'owner']))
                                <option value="admin">Administrator</option>
                                <option value="owner">Owner / Direktur</option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <x-input-label for="is_active_{{ $user->id }}" :value="__('Status Akun')" class="mb-1" />
                            <select id="is_active_{{ $user->id }}" name="is_active" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-lg shadow-sm text-sm">
                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                        </div>

                        <div x-show="role === 'technician'" x-transition class="pt-2" x-cloak>
                            <x-input-label for="specialization_{{ $user->id }}" :value="__('Spesialisasi Teknis')" />
                            <select id="specialization_{{ $user->id }}" name="specialization" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-lg shadow-sm text-sm">
                                <option value="">-- Pilih --</option>
                                <optgroup label="Preparation">
                                    <option value="Washing" {{ $user->specialization === 'Washing' ? 'selected' : '' }}>Washing</option>
                                    <option value="Sol Repair" {{ $user->specialization === 'Sol Repair' ? 'selected' : '' }}>Sol Repair</option>
                                    <option value="Upper Repair" {{ $user->specialization === 'Upper Repair' ? 'selected' : '' }}>Upper Repair</option>
                                </optgroup>
                                <optgroup label="Repaint & Treatment">
                                    <option value="Repaint" {{ $user->specialization === 'Repaint' ? 'selected' : '' }}>Repaint</option>
                                    <option value="Treatment" {{ $user->specialization === 'Treatment' ? 'selected' : '' }}>Treatment</option>
                                </optgroup>
                                <optgroup label="QC">
                                    <option value="Jahit" {{ $user->specialization === 'Jahit' ? 'selected' : '' }}>Jahit</option>
                                    <option value="Clean Up" {{ $user->specialization === 'Clean Up' ? 'selected' : '' }}>Clean Up</option>
                                    <option value="PIC QC" {{ $user->specialization === 'PIC QC' ? 'selected' : '' }}>PIC QC</option>
                                </optgroup>
                            </select>
                            <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                        </div>

                        <div class="pt-4 border-t border-dashed border-gray-200 mt-2">
                            <x-input-label for="password_{{ $user->id }}" :value="__('Ubah Password (Opsional)')" />
                            <x-text-input id="password_{{ $user->id }}" class="block mt-1 w-full text-sm" type="password" name="password" placeholder="Kosongkan jika tetap" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            <x-text-input id="password_confirmation_{{ $user->id }}" class="block mt-2 w-full text-sm" type="password" name="password_confirmation" placeholder="Konfirmasi Password" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Access Rights --}}
            <div class="lg:col-span-8 p-6 space-y-6">
                <div class="flex items-center justify-between mb-4">
                     <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        Hak Akses Modul
                    </h3>
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs rounded-full border border-amber-200">
                        Pilih modul yang dapat diakses
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($allDivisions as $division)
                    <div class="p-4 bg-white border border-gray-100 rounded-xl shadow-sm">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-{{ $division['color'] }}-400"></span> {{ $division['title'] }}
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($division['modules'] as $key => $label)
                                <label class="group relative cursor-pointer">
                                    <input type="checkbox" name="access_rights[]" value="{{ $key }}" 
                                           {{ in_array($key, $user->access_rights ?? []) ? 'checked' : '' }}
                                           class="peer sr-only">
                                    <div class="p-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 transition-all duration-200 peer-checked:border-{{ $division['color'] }}-500 peer-checked:ring-1 peer-checked:ring-{{ $division['color'] }}-500 peer-checked:bg-{{ $division['color'] }}-50/50 dark:peer-checked:bg-{{ $division['color'] }}-900/20">
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-md border border-gray-300 dark:border-gray-600 flex items-center justify-center text-white peer-checked:bg-{{ $division['color'] }}-500 peer-checked:border-{{ $division['color'] }}-500 transition-colors">
                                                <svg class="w-3.5 h-3.5 opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-20 peer-checked:hidden transition-opacity text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 peer-checked:text-{{ $division['color'] }}-700 dark:peer-checked:text-{{ $division['color'] }}-400 select-none">{{ $label }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 p-4 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-800 flex items-start gap-3">
                    <svg class="w-5 h-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <h5 class="text-sm font-bold text-teal-800 dark:text-teal-300">Catatan Administrator</h5>
                        <p class="text-xs text-teal-600 dark:text-teal-400 mt-1">
                            User dengan role <strong>Admin</strong> secara otomatis memiliki akses penuh ke semua modul, terlepas dari pilihan di atas.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 rounded-b-lg">
            <button type="button" x-on:click="$dispatch('close')" 
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all shadow-sm">
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
</x-modal>

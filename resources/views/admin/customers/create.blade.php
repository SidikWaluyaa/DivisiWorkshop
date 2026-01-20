<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">Tambah Customer Baru</h2>
                <div class="text-xs font-medium opacity-90">Master Data Customer</div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.customers.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        {{-- Basic Info --}}
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                Informasi Dasar
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('name') border-red-500 @enderror">
                                    @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        No. WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" required
                                           placeholder="08xxxxxxxxxx"
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('phone') border-red-500 @enderror">
                                    @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email
                                    </label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                           placeholder="customer@example.com"
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('email') border-red-500 @enderror">
                                    @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Address Info --}}
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                Alamat
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Alamat Lengkap
                                    </label>
                                    <textarea name="address" rows="3"
                                              class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                                    @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kota
                                    </label>
                                    <input type="text" name="city" value="{{ old('city') }}"
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('city') border-red-500 @enderror">
                                    @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Provinsi
                                    </label>
                                    <input type="text" name="province" value="{{ old('province') }}"
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('province') border-red-500 @enderror">
                                    @error('province')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kode Pos
                                    </label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('postal_code') border-red-500 @enderror">
                                    @error('postal_code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan
                            </label>
                            <textarea name="notes" rows="3" placeholder="Catatan tambahan tentang customer..."
                                      class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.customers.index') }}" 
                           class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 font-semibold transition-all shadow-md">
                            Simpan Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

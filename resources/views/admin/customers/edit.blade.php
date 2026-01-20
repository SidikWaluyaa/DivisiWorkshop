<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">Edit Customer</h2>
                <div class="text-xs font-medium opacity-90">{{ $customer->name }}</div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('name') border-red-500 @enderror">
                                    @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        No. WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required
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
                                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
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
                                              class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('address') border-red-500 @enderror">{{ old('address', $customer->address) }}</textarea>
                                    @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kota
                                    </label>
                                    <input type="text" name="city" value="{{ old('city', $customer->city) }}"
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('city') border-red-500 @enderror">
                                    @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Provinsi
                                    </label>
                                    <input type="text" name="province" value="{{ old('province', $customer->province) }}"
                                           class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('province') border-red-500 @enderror">
                                    @error('province')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kode Pos
                                    </label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}"
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
                                      class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500 @error('notes') border-red-500 @enderror">{{ old('notes', $customer->notes) }}</textarea>
                            @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus customer ini? Semua foto akan ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors">
                                Hapus Customer
                            </button>
                        </form>

                        <div class="flex gap-3">
                            <a href="{{ route('admin.customers.show', $customer) }}" 
                               class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 font-semibold transition-all shadow-md">
                                Update Customer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

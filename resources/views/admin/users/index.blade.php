<x-app-layout>
    <x-slot name="header">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Master Data User / Teknisi') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ selected: [], role: 'user' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-white">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Toolbar: Bulk Actions & Add Button --}}
                    <div class="flex justify-between items-center mb-4">
                        <form action="{{ route('admin.users.bulk-destroy') }}" method="POST" onsubmit="return confirm('Yakin hapus ' + this.selected.length + ' user terpilih?')">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selected">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                    :disabled="selected.length === 0"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow-md flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus (<span x-text="selected.length">0</span>) Terpilih
                            </button>
                        </form>

                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-user-modal')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            + Tambah User
                        </button>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input type="checkbox" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @click="selected = $el.checked ? {{ json_encode($users->pluck('id')) }} : []"
                                           :checked="selected.length === {{ $users->count() }} && {{ $users->count() }} > 0">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    @if($user->specialization)
                                        <div class="text-xs text-gray-500 mt-1">
                                            ({{ $user->specialization }})
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-user-modal-{{ $user->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <x-modal name="edit-user-modal-{{ $user->id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit User</h2>
                                    <div class="mt-4">
                                        <x-input-label for="name" :value="__('Nama')" />
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$user->name" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="email" :value="__('Email')" />
                                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$user->email" required />
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="phone" :value="__('No. HP / WA (Optional)')" />
                                        <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="$user->phone" placeholder="628xxx" />
                                    </div>
                                    <div class="mt-4" x-data="{ currentRole: '{{ $user->role }}' }">
                                        <x-input-label for="role" :value="__('Role')" />
                                        <select id="role" name="role" x-model="currentRole" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                            <option value="user">User</option>
                                            <option value="technician">Technician</option>
                                            <option value="pic">PIC Material</option>
                                            <option value="gudang">Gudang</option>
                                            <option value="admin">Admin</option>
                                        </select>

                                        <div x-show="currentRole === 'technician'" class="mt-4">
                                            <x-input-label for="specialization" :value="__('Spesialisasi')" />
                                            <select id="specialization" name="specialization" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                <option value="">-- Pilih Spesialisasi --</option>
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
                                                <optgroup label="Gudang / Material">
                                                    <option value="PIC Material Sol" {{ $user->specialization === 'PIC Material Sol' ? 'selected' : '' }}>PIC Material Sol</option>
                                                    <option value="PIC Material Upper" {{ $user->specialization === 'PIC Material Upper' ? 'selected' : '' }}>PIC Material Upper</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <x-input-label for="password" :value="__('Password (Isi jika ingin mengubah)')" />
                                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                    </div>
                                     <div class="mt-4">
                                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                                        <x-primary-button class="ms-3">{{ __('Simpan Perubahan') }}</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-user-modal" :show="false" focusable>
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tambah User Baru</h2>
            <div class="mt-4">
                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
            </div>
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required />
            </div>
            <div class="mt-4">
                <x-input-label for="phone" :value="__('No. HP / WA (Optional)')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" placeholder="628xxx" />
            </div>
            <div class="mt-4">
                <x-input-label for="role" :value="__('Role')" />
                <select id="role" name="role" x-model="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="user">User</option>
                    <option value="technician">Technician</option>
                    <option value="pic">PIC Material</option>
                    <option value="gudang">Gudang</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="mt-4" x-show="role === 'technician'">
                <x-input-label for="specialization" :value="__('Spesialisasi')" />
                <select id="specialization" name="specialization" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">-- Pilih Spesialisasi --</option>
                    <optgroup label="Preparation">
                        <option value="Washing">Washing</option>
                        <option value="Sol Repair">Sol Repair</option>
                        <option value="Upper Repair">Upper Repair</option>
                    </optgroup>
                    <optgroup label="Repaint & Treatment">
                        <option value="Repaint">Repaint</option>
                        <option value="Treatment">Treatment</option>
                    </optgroup>
                    <optgroup label="QC">
                        <option value="Jahit">Jahit</option>
                        <option value="Clean Up">Clean Up</option>
                        <option value="PIC QC">PIC QC</option>
                    </optgroup>
                    <optgroup label="Gudang / Material">
                        <option value="PIC Material Sol">PIC Material Sol</option>
                        <option value="PIC Material Upper">PIC Material Upper</option>
                    </optgroup>
                </select>
            </div>
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            </div>
                <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="ms-3">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

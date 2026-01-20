@props(['order', 'step', 'readOnly' => false, 'title' => null])

<div x-data='{
    orderId: {{ $order->id }},
    step: "{{ $step }}",
    photos: @json($order->photos->where('step', $step)->map(fn($p) => ['id' => $p->id, 'url' => Storage::url($p->file_path)])->values()),
    uploading: false,

    async uploadPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.uploading = true;
        const formData = new FormData();
        formData.append("photo", file);
        formData.append("step", this.step);
        formData.append("is_public", 1);

        try {
            const response = await fetch(`/orders/${this.orderId}/photos`, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
                    "Accept": "application/json"
                }
            });

            const data = await response.json();

            if (response.ok) {
                if (data.success) {
                    this.photos.push({ id: data.photo.id, url: data.url });
                } else {
                    alert(data.message || "Gagal upload foto.");
                }
            } else {
                console.error("Server Error:", data);
                alert(data.message || "Gagal upload foto. Silahkan coba lagi.");
            }
        } catch (error) {
            console.error("Network Error:", error);
            alert("Terjadi kesalahan koneksi.");
        } finally {
            this.uploading = false;
            event.target.value = ""; 
        }
    },

    async deletePhoto(id) {
        if (!confirm("Hapus foto ini?")) return;

        try {
            const response = await fetch(`/photos/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
                    "Accept": "application/json"
                }
            });

            if (response.ok) {
                this.photos = this.photos.filter(p => p.id !== id);
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Gagal menghapus foto.");
        }
    },

    viewPhoto(url) {
        window.open(url, "_blank");
    }
}' class="mt-4 border-t border-gray-100 pt-4">
    <div class="flex justify-between items-center mb-3">
        <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ $title ?? 'Dokumentasi ' . ucwords(strtolower(str_replace('_', ' ', $step))) }}
        </h4>
        
        @if(!$readOnly)
            <label class="cursor-pointer inline-flex items-center gap-1 px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-bold text-gray-600 transition-colors">
                <input type="file" class="hidden" accept="image/*" @change="uploadPhoto($event)">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Foto
            </label>
        @endif
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-3 md:grid-cols-4 gap-2 mb-2">
        <template x-for="photo in photos" :key="photo.id">
            <div class="relative group aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                <img :src="photo.url" class="w-full h-full object-cover cursor-pointer hover:scale-110 transition-transform duration-300" @click="viewPhoto(photo.url)">
                
                @if(!$readOnly)
                    <button @click="deletePhoto(photo.id)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                @endif
            </div>
        </template>
        
        <!-- Loading State -->
        <div x-show="uploading" class="aspect-square bg-gray-50 rounded-lg flex items-center justify-center border border-gray-200 border-dashed animate-pulse">
            <svg class="w-6 h-6 text-gray-300 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
</div>

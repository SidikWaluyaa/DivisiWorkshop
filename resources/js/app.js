import "./bootstrap";

import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

window.Swal = Swal;
window.Chart = Chart;

// Define photoUploader component
document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(collapse);

    window.Alpine.data("photoUploader", (config) => ({
        orderId: config.orderId,
        step: config.step,
        photos: config.photos || [],
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
                const response = await fetch(
                    `/work-orders/${this.orderId}/photos`,
                    {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            Accept: "application/json",
                        },
                    }
                );

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.photos.push({ id: data.photo.id, url: data.url });
                    } else {
                        alert(data.message || "Gagal upload foto.");
                    }
                } else {
                    const errorText = await response.text();
                    console.error("Server Error:", errorText);
                    alert("Gagal upload foto. Silahkan coba lagi.");
                }
            } catch (error) {
                console.error("Network Error:", error);
                alert("Terjadi kesalahan koneksi.");
            } finally {
                this.uploading = false;
                event.target.value = ""; // Reset input
            }
        },

        async deletePhoto(id) {
            if (!confirm("Hapus foto ini?")) return;

            try {
                const response = await fetch(`/photos/${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                    },
                });

                if (response.ok) {
                    this.photos = this.photos.filter((p) => p.id !== id);
                }
            } catch (error) {
                console.error("Error:", error);
            }
        },

        viewPhoto(url) {
            window.open(url, "_blank");
        },
    }));

    // Reference photo uploader for CS handover (multi-file with preview)
    window.Alpine.data('refPhotoUploader', (id) => ({
        id: id,
        files: [],
        previews: [],
        coverIndex: 0,
        refIndex: 0,
        addFiles(fileList) {
            for (let i = 0; i < fileList.length; i++) {
                const file = fileList[i];
                if (!file.type.startsWith('image/')) continue;
                this.files.push(file);
                const reader = new FileReader();
                reader.onload = (e) => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            }
            this.syncInput();
        },
        removeFile(index) {
            this.files.splice(index, 1);
            this.previews.splice(index, 1);
            
            // Adjust indices
            if (this.coverIndex === index) this.coverIndex = 0;
            else if (this.coverIndex > index) this.coverIndex--;
            
            if (this.refIndex === index) this.refIndex = 0;
            else if (this.refIndex > index) this.refIndex--;
            
            this.syncInput();
        },
        setCover(index) {
            this.coverIndex = index;
        },
        setRef(index) {
            this.refIndex = index;
        },
        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            this.$refs.fileInput.files = dt.files;
        }
    }));
});

// Optimized Global Real-Time Timer for In-Progress Orders
let realTimeTimerInterval = null;

function updateTimer(element, startedAt) {
    const now = new Date();
    const elapsed = Math.floor((now - startedAt) / 1000); // seconds
    
    if (elapsed < 0) {
        element.textContent = '0m 0s';
        return;
    }
    
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = elapsed % 60;
    
    let display = '';
    if (hours > 0) {
        display = `${hours}h ${minutes}m`;
    } else if (minutes > 0) {
        display = `${minutes}m ${seconds}s`;
    } else {
        display = `${seconds}s`;
    }
    
    element.textContent = display;
    
    // Add color coding based on duration
    element.classList.remove('text-green-600', 'text-yellow-600', 'text-red-600');
    if (elapsed < 1800) { // < 30 min
        element.classList.add('text-green-600');
    } else if (elapsed < 3600) { // < 1 hour
        element.classList.add('text-yellow-600');
    } else { // > 1 hour
        element.classList.add('text-red-600');
    }
}

function updateAllTimers() {
    const timerElements = document.querySelectorAll('[data-started-at]');
    timerElements.forEach(el => {
        const startedAtStr = el.dataset.startedAt;
        if (!startedAtStr) return;
        
        const startedAt = new Date(startedAtStr);
        updateTimer(el, startedAt);
    });
}

function initRealTimeTimers() {
    updateAllTimers();
    if (!realTimeTimerInterval) {
        realTimeTimerInterval = setInterval(updateAllTimers, 1000);
    }
}

// Dom listeners
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRealTimeTimers);
} else {
    initRealTimeTimers();
}

// Hook into Livewire events
document.addEventListener('livewire:navigated', () => {
    initRealTimeTimers();
});
document.addEventListener('livewire:update', () => {
    initRealTimeTimers();
});
document.addEventListener('alpine:initialized', () => {
    setTimeout(initRealTimeTimers, 100);
});


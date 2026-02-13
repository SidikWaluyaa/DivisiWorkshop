import "./bootstrap";

import Alpine from "alpinejs";
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

window.Swal = Swal;
window.Chart = Chart;

Alpine.plugin(collapse);

window.Alpine = Alpine;

// Define photoUploader component
document.addEventListener("alpine:init", () => {
    Alpine.data("photoUploader", (config) => ({
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
    Alpine.data('refPhotoUploader', (id) => ({
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

Alpine.start();

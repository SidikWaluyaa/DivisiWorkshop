/**
 * Preparation Module JavaScript
 * Handles bulk actions, station updates, and modal interactions
 */

// Initialize Alpine Store
// Handle both cases: Alpine not loaded yet, and Alpine already loaded
function initPreparationStore() {
    const storeDefinition = {
        items: [],
        
        toggle(id) {
            id = String(id);
            if (this.items.includes(id)) {
                this.items = this.items.filter(i => i !== id);
            } else {
                this.items.push(id);
            }
        },

        includes(id) {
            return this.items.includes(String(id));
        },

        count() {
            return this.items.length;
        },

        clear() {
            this.items = [];
        },
        
        getIds() {
            return this.items;
        }
    };

    // Check if Alpine is already loaded
    if (window.Alpine) {
        window.Alpine.store('preparation', storeDefinition);
        console.log('✅ Alpine store registered (Alpine already loaded)');
    } else {
        // Alpine not loaded yet, wait for alpine:init event
        document.addEventListener('alpine:init', () => {
            window.Alpine.store('preparation', storeDefinition);
            console.log('✅ Alpine store registered (via alpine:init)');
        });
    }
}

// Initialize store immediately
initPreparationStore();


/**
 * Bulk Action Handler
 * @param {string} action - Action type: 'assign', 'finish', 'approve'
 */
window.bulkAction = function(action) {
    let selectedItems = Alpine.store('preparation').getIds();
    
    if (selectedItems.length === 0) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Pilih item', 
            text: 'Tidak ada order yang dipilih.' 
        });
        return;
    }

    let techId = null;
    let title = 'Konfirmasi';
    let text = `Proses ${selectedItems.length} order yang dipilih?`;
    let confirmBtnText = 'Ya, Proses!';
    let confirmBtnColor = '#3085d6';

    if (action === 'assign' || action === 'start') {
        const selectEl = document.getElementById('bulk-tech-select');
        if (selectEl && selectEl.value) {
            techId = selectEl.value;
        } else if (action === 'assign') {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Pilih Teknisi', 
                text: 'Silakan pilih teknisi untuk Assign.' 
            });
            return;
        }
        title = 'Konfirmasi Assign';
        text = `Assign ${selectedItems.length} order ke teknisi dan langsung mulai pekerjaan?`;
        confirmBtnText = 'Ya, Assign & Mulai!';
    } else if (action === 'approve') {
        title = 'Konfirmasi Approve';
        text = `Approve ${selectedItems.length} order dan pindahkan ke proses Sortir?`;
        confirmBtnText = 'Ya, Approve & Sortir!';
        confirmBtnColor = '#10B981';
    } else if (action === 'finish') {
        title = 'Selesaikan Proses';
        text = `Tandai ${selectedItems.length} order sebagai selesai pada tahap ini?`;
        confirmBtnText = 'Ya, Selesaikan!';
        confirmBtnColor = '#059669';
    }

    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmBtnColor,
        cancelButtonColor: '#6B7280',
        confirmButtonText: confirmBtnText,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get active tab from data attribute
            const activeTabEl = document.querySelector('[data-active-tab]');
            const activeTab = activeTabEl ? activeTabEl.dataset.activeTab : 'washing';
            
            // Map tab names to station types
            let type = 'washing';
            if (activeTab === 'sol') type = 'sol';
            if (activeTab === 'upper') type = 'upper';

            // Get CSRF token and route from meta tags
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const bulkUpdateUrl = document.querySelector('meta[name="bulk-update-url"]').content;

            fetch(bulkUpdateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    ids: selectedItems,
                    action: action === 'assign' ? 'start' : action,
                    type: type, 
                    technician_id: techId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && (!data.errors || data.errors.length === 0)) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    let errorMessage = data.message || 'Terjadi kesalahan saat memproses data.';
                    if (data.errors && data.errors.length > 0) {
                        errorMessage += "\n\nDetail:\n" + data.errors.join("\n");
                    }
                    
                    Swal.fire({
                        icon: data.success ? 'warning' : 'error',
                        title: data.success ? 'Selesai dengan Peringatan' : 'Gagal',
                        text: errorMessage,
                        confirmButtonText: 'Tutup'
                    }).then(() => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan pada request.'
                });
            });
        }
    });
}

/**
 * Update Station Status
 * @param {number} id - Order ID
 * @param {string} type - Station type: 'washing', 'sol', 'upper'
 * @param {string} action - Action: 'start', 'finish'
 * @param {string|null} finishedAt - Completion timestamp
 */
window.updateStation = function(id, type, action = 'finish', finishedAt = null) {
    let techId = null;
    if (action === 'start') {
        const selectId = `tech-${type}-${id}`;
        const selectEl = document.getElementById(selectId);
        if (!selectEl) {
            console.error("Select Element not found:", selectId);
            alert("Error: Technician select not found for " + selectId);
            return;
        }
        techId = selectEl.value;
        if (!techId) {
            alert('Silakan pilih teknisi terlebih dahulu.');
            return;
        }
    }

    if (action === 'start' && !confirm('Mulai proses ini?')) return;

    console.log('Sending Update:', { id, type, action, finishedAt });

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const updateStationUrl = `/preparation/${id}/update-station`;

    fetch(updateStationUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            type: type, 
            action: action,
            technician_id: techId,
            finished_at: finishedAt
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({})); 
        if (!response.ok) {
            throw new Error(data.message || response.statusText || 'Server Error ' + response.status);
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Status berhasil diperbarui.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload(); 
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan: ' + error.message
        });
    });
}

/**
 * Confirm Approve Action
 * @param {number} id - Order ID
 */
window.confirmApprove = function(id) {
    Swal.fire({
        title: 'Preparation Selesai?',
        text: "Lanjutkan ke proses Sortir?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Lanjut Sortir!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('approve-form-' + id).submit();
        }
    });
}

/**
 * Toggle All Checkboxes
 * @param {Event} e - Click event
 */
window.toggleAll = function(e) {
    const checkboxes = document.querySelectorAll('.wo-checkbox');
    let selected = [];
    if (e.target.checked) {
        checkboxes.forEach(cb => {
            if (cb.value && cb.value !== 'on') selected.push(cb.value);
        });
    }
    Alpine.store('preparation').items = selected;
}

/**
 * Open Report Modal
 * @param {number} id - Order ID
 */
window.openReportModal = function(id) {
    document.getElementById('report_work_order_id').value = id;
    const modal = document.getElementById('reportModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}

/**
 * Close Report Modal
 */
window.closeReportModal = function() {
    const modal = document.getElementById('reportModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
}


console.log('✅ Preparation module loaded');


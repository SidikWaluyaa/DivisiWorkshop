<script>
    // General Functions
    function sendEmailNotification(id) {
        Swal.fire({
            title: 'Kirim Nota Email?',
            text: "Sistem akan mengirimkan nota digital ke email customer.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/reception/${id}/send-email`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(response.statusText);
                    return response.json();
                })
                .catch(error => Swal.showValidationMessage(`Request failed: ${error}`));
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: result.value.success ? 'Terkirim!' : 'Gagal!',
                    text: result.value.message,
                    icon: result.value.success ? 'success' : 'error'
                });
            }
        });
    }

    // Modal Management
    function openEditShoeModal(orderId, brand, size, color, category) {
        const placeholders = ['', 'unknown', '-', 'item'];
        document.getElementById('editShoeOrderId').value = orderId;
        document.getElementById('editShoeBrand').value = placeholders.includes((brand || '').toLowerCase().trim()) ? '' : brand;
        document.getElementById('editShoeSize').value = placeholders.includes((size || '').toLowerCase().trim()) ? '' : size;
        document.getElementById('editShoeColor').value = placeholders.includes((color || '').toLowerCase().trim()) ? '' : color;
        document.getElementById('editShoeCategory').value = (category && placeholders.includes(category.toLowerCase().trim())) ? '' : (category || '');
        document.getElementById('editShoeModal').classList.remove('hidden');
    }

    function closeEditShoeModal() {
        document.getElementById('editShoeModal').classList.add('hidden');
        document.getElementById('editShoeForm').reset();
    }

    function updateShoeInfo(event) {
        event.preventDefault();
        const orderId = document.getElementById('editShoeOrderId').value;
        const body = {
            shoe_brand: document.getElementById('editShoeBrand').value,
            shoe_size: document.getElementById('editShoeSize').value,
            shoe_color: document.getElementById('editShoeColor').value,
            category: document.getElementById('editShoeCategory').value
        };
        
        fetch(`/reception/${orderId}/update-shoe-info`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false });
                closeEditShoeModal();
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
            }
        })
        .catch(error => Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan: ' + error.message }));
    }

    function openCreateOrderModal() { document.getElementById('createOrderModal').classList.remove('hidden'); }
    function closeCreateOrderModal() {
        document.getElementById('createOrderModal').classList.add('hidden');
        document.getElementById('createOrderForm').reset();
    }

    function submitCreateOrder(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        fetch('/reception/store', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false });
                closeCreateOrderModal();
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan' });
            }
        })
        .catch(error => Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan: ' + error.message }));
    }

    function openEditOrderModal(order) {
        document.getElementById('edit_order_id').value = order.id;
        document.getElementById('edit_spk_number').value = order.spk_number;
        document.getElementById('edit_customer_name').value = order.customer_name;
        document.getElementById('edit_customer_phone').value = order.customer_phone;
        document.getElementById('edit_notes').value = order.notes || '';
        document.getElementById('edit_technician_notes').value = order.technician_notes || '';
        document.getElementById('edit_priority').value = order.priority;
        document.getElementById('editOrderModal').classList.remove('hidden');
    }

    function closeEditOrderModal() {
        document.getElementById('editOrderModal').classList.add('hidden');
        document.getElementById('editOrderForm').reset();
    }

    function submitEditOrder(event) {
        event.preventDefault();
        const orderId = document.getElementById('edit_order_id').value;
        const formData = new FormData(event.target);
        fetch(`/reception/${orderId}/update-order`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-HTTP-Method-Override': 'PATCH', 'Accept': 'application/json' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false });
                closeEditOrderModal();
                location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
            }
        })
        .catch(error => Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan sistem.' }));
    }

    function openEditEmailModal(orderId, currentEmail) {
        document.getElementById('editOrderId').value = orderId;
        document.getElementById('editEmailInput').value = currentEmail || '';
        document.getElementById('editEmailModal').classList.remove('hidden');
    }

    function closeEditEmailModal() {
        document.getElementById('editEmailModal').classList.add('hidden');
        document.getElementById('editEmailForm').reset();
    }

    function updateEmail(event) {
        event.preventDefault();
        const orderId = document.getElementById('editOrderId').value;
        const email = document.getElementById('editEmailInput').value;
        fetch(`/reception/${orderId}/update-email`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false });
                closeEditEmailModal();
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
            }
        })
        .catch(error => Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan: ' + error.message }));
    }

    // Detail Modal
    function openDetailModal(order, services, accessories) {
        document.getElementById('detail_spk_number').innerText = `SPK: ${order.spk_number}`;
        document.getElementById('detail_entry_date').innerText = `Masuk: ${new Date(order.entry_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}`;
        document.getElementById('detail_estimation_date').innerText = `Estimasi: ${new Date(order.estimation_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}`;
        document.getElementById('detail_customer_name').innerText = order.customer_name || '-';
        document.getElementById('detail_customer_phone').innerText = order.customer_phone || '-';
        document.getElementById('detail_customer_email').innerText = order.customer_email || '-';
        document.getElementById('detail_customer_address').innerText = order.customer_address || '-';
        document.getElementById('detail_shoe_brand').innerText = order.shoe_brand || '-';
        document.getElementById('detail_category').innerText = order.category || '-';
        document.getElementById('detail_shoe_color').innerText = order.shoe_color || '-';
        document.getElementById('detail_shoe_size').innerText = order.shoe_size || '-';
        
        const priorityEl = document.getElementById('detail_priority');
        priorityEl.innerText = order.priority || 'NORMAL';
        priorityEl.className = 'px-2 py-0.5 rounded text-[10px] font-black uppercase text-white ' + 
            (order.priority === 'Prioritas' || order.priority === 'Urgent' ? 'bg-red-500' : 'bg-teal-500');
        
        const accList = document.getElementById('detail_accessories_list');
        accList.innerHTML = '';
        const labels = {tali: 'Tali', insole: 'Insole', box: 'Box', lainnya: 'Lainnya'};
        for (const [key, label] of Object.entries(labels)) {
            const val = accessories ? (accessories[key] || 'T') : 'T';
            let statusClass = 'bg-gray-200 text-gray-500';
            if (val === 'S') statusClass = 'bg-blue-100 text-blue-700';
            if (val === 'N') statusClass = 'bg-orange-100 text-orange-700';
            accList.innerHTML += `<div class="flex items-center justify-between p-2 rounded bg-white border border-gray-100"><span class="text-gray-500">${label}</span><span class="px-1.5 py-0.5 rounded font-black ${statusClass}">${val}</span></div>`;
        }

        const svcList = document.getElementById('detail_services_list');
        svcList.innerHTML = '';
        let total = 0;
        if (services && services.length > 0) {
            services.forEach(svc => {
                let price = parseFloat(svc.price || 0);
                if(svc.pivot && svc.pivot.cost) price = parseFloat(svc.pivot.cost);
                total += price;
                svcList.innerHTML += `<div class="flex justify-between items-start text-sm p-2 bg-gray-50 rounded border border-gray-100"><div><div class="font-bold text-gray-800">${svc.name === 'Custom Service' && svc.pivot && svc.pivot.custom_name ? svc.pivot.custom_name : svc.name}</div><div class="text-[10px] text-gray-400 uppercase">${svc.category || ''}</div></div><div class="font-bold text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(price)}</div></div>`;
            });
        } else {
            svcList.innerHTML = '<p class="text-xs text-center text-gray-400 italic">Tidak ada data layanan</p>';
        }
        document.getElementById('detail_total_price').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        const noteEl = document.getElementById('detail_qc_notes');
        if (noteEl) noteEl.innerText = order.warehouse_qc_notes || 'Tidak ada catatan';
        document.getElementById('detail_cs_notes').innerText = order.notes || '-';
        document.getElementById('detail_technician_notes').innerText = order.technician_notes || '-';
        document.getElementById('orderDetailModal').classList.remove('hidden');
    }

    function closeDetailModal() { document.getElementById('orderDetailModal').classList.add('hidden'); }

    // Bulk Actions
    function bulkDirectToPrep() {
        let ids = [];
        try {
            const alpineEl = document.querySelector('[x-data]');
            if (alpineEl && alpineEl.__x) ids = alpineEl.__x.$data.selectedItems || [];
        } catch (e) { console.error('Alpine access error:', e); }

        if (ids.length === 0) {
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile:checked' : '.check-item-desktop:checked';
            ids = Array.from(document.querySelectorAll(selector)).map(cb => cb.value);
        }

        if (ids.length === 0) {
            Swal.fire('Peringatan', 'Pilih item terlebih dahulu.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Bulk Direct to Prep',
            text: `Langsung kirim ${ids.length} order ke Preparation?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route('reception.bulk-skip-assessment') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) Swal.fire('Berhasil!', data.message, 'success').then(() => window.location.reload());
                    else Swal.fire('Gagal', data.message, 'error');
                });
            }
        });
    }

    function submitBulkDelete() {
        let selectAll = false, totalRecords = 0, selectedCount = 0, ids = [];
        try {
            const alpineEl = document.getElementById('reception-main-container');
            if (alpineEl && alpineEl.__x) {
                const data = alpineEl.__x.$data;
                selectAll = data.selectAllMode;
                totalRecords = data.totalRecords;
                selectedCount = data.selectedItems.length;
                ids = data.selectedItems;
            }
        } catch (e) {}

        if (selectedCount === 0) {
             const selector = window.innerWidth < 1024 ? '.check-item-mobile:checked' : '.check-item-desktop:checked';
             ids = Array.from(document.querySelectorAll(selector)).map(cb => cb.value);
             selectedCount = ids.length;
        }

        const countText = selectAll ? totalRecords : selectedCount;
        if (countText === 0) { Swal.fire('Peringatan', 'Pilih item terlebih dahulu.', 'warning'); return; }

        Swal.fire({
            title: 'Hapus Massal',
            html: `Hapus <strong>${countText}</strong> data secara permanen?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('bulk-delete-form');
                form.querySelectorAll('input[name="ids[]"], input[name="select_all"]').forEach(el => el.remove());
                if (selectAll) {
                    let input = document.createElement('input'); input.type = 'hidden'; input.name = 'select_all'; input.value = '1'; form.appendChild(input);
                } else {
                    ids.forEach(id => {
                        let input = document.createElement('input'); input.type = 'hidden'; input.name = 'ids[]'; input.value = id; form.appendChild(input);
                    });
                }
                form.submit();
            }
        });
    }

    // Regional Fetching
    const REGIONAL_API_BASE = '/regional';
    function fetchManualProvinces() {
        const select = document.getElementById('manual_select_province');
        if (!select) return;
        fetch(`${REGIONAL_API_BASE}/provinces`).then(r => r.json()).then(data => {
            data.forEach(prov => {
                const opt = document.createElement('option'); opt.value = prov.id; opt.text = prov.name; opt.dataset.name = prov.name; select.appendChild(opt);
            });
        });
    }

    function handleManualProvinceChange(el) {
        const provId = el.value;
        document.getElementById('manual_input_province').value = el.options[el.selectedIndex].dataset.name || '';
        const citySelect = document.getElementById('manual_select_city');
        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>'; citySelect.disabled = true;
        if (provId) {
            fetch(`${REGIONAL_API_BASE}/regencies/${provId}`).then(r => r.json()).then(data => {
                citySelect.disabled = false;
                data.forEach(city => {
                    const opt = document.createElement('option'); opt.value = city.id; opt.text = city.name; opt.dataset.name = city.name; citySelect.appendChild(opt);
                });
            });
        }
    }

    // ... (other handle handlers) ...
    function handleManualCityChange(el) {
        const cityId = el.value;
        document.getElementById('manual_input_city').value = el.options[el.selectedIndex].dataset.name || '';
        const distSelect = document.getElementById('manual_select_district');
        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>'; distSelect.disabled = true;
        if (cityId) {
            fetch(`${REGIONAL_API_BASE}/districts/${cityId}`).then(r => r.json()).then(data => {
                distSelect.disabled = false;
                data.forEach(dist => {
                    const opt = document.createElement('option'); opt.value = dist.id; opt.text = dist.name; opt.dataset.name = dist.name; distSelect.appendChild(opt);
                });
            });
        }
    }

    function handleManualDistrictChange(el) {
        const distId = el.value;
        document.getElementById('manual_input_district').value = el.options[el.selectedIndex].dataset.name || '';
        const villSelect = document.getElementById('manual_select_village');
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>'; villSelect.disabled = true;
        if (distId) {
            fetch(`${REGIONAL_API_BASE}/villages/${distId}`).then(r => r.json()).then(data => {
                villSelect.disabled = false;
                data.forEach(vill => {
                    const opt = document.createElement('option'); opt.value = vill.id; opt.text = vill.name; opt.dataset.name = vill.name; villSelect.appendChild(opt);
                });
            });
        }
    }

    function handleManualVillageChange(el) {
        document.getElementById('manual_input_village').value = el.options[el.selectedIndex].dataset.name || '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('manual_select_province')) fetchManualProvinces();
        
        // Modal events
        document.querySelectorAll('#editShoeModal, #createOrderModal, #orderDetailModal, #editEmailModal, #rackStorageModal').forEach(modal => {
            modal.addEventListener('click', function(e) { if (e.target === this) this.classList.add('hidden'); });
        });
    });

    // Alpine Service Selector
    function serviceSelector() {
        return {
            masterServices: @json($services),
            selectedServices: [],
            showServiceModal: false,
            serviceForm: { category: '', service_id: '', custom_name: '', price: 0, details: [], newDetail: '' },
            get uniqueCategories() { return [...new Set(this.masterServices.map(s => s.category))].filter(Boolean); },
            get filteredServices() { return this.masterServices.filter(s => s.category === this.serviceForm.category); },
            selectService() {
                if (this.serviceForm.service_id === 'custom') { this.serviceForm.custom_name = ''; this.serviceForm.price = 0; }
                else { const s = this.masterServices.find(x => x.id == this.serviceForm.service_id); if(s) { this.serviceForm.custom_name = s.name; this.serviceForm.price = s.price; } }
            },
            addDetail() { if (this.serviceForm.newDetail.trim()) { this.serviceForm.details.push(this.serviceForm.newDetail.trim()); this.serviceForm.newDetail = ''; } },
            removeDetail(i) { this.serviceForm.details.splice(i, 1); },
            saveService() {
                if (!this.serviceForm.category || !this.serviceForm.service_id) return Swal.fire('Error', 'Pilih kategori & layanan', 'error');
                this.selectedServices.push({ 
                    service_id: this.serviceForm.service_id, 
                    name: this.serviceForm.service_id === 'custom' ? this.serviceForm.custom_name : this.masterServices.find(s => s.id == this.serviceForm.service_id).name,
                    price: parseInt(this.serviceForm.price),
                    details: [...this.serviceForm.details]
                });
                this.serviceForm = { category: '', service_id: '', custom_name: '', price: 0, details: [], newDetail: '' };
                this.showServiceModal = false;
            },
            removeService(i) { this.selectedServices.splice(i, 1); },
            calculateTotal() { return this.selectedServices.reduce((s, x) => s + x.price, 0); }
        };
    }

    // Rack Storage Modal Functions
    function confirmReceive(id, spkNumber) {
        document.getElementById('storage_order_id').value = id;
        document.getElementById('storage_spk_number').innerText = spkNumber;
        document.getElementById('storage_rack_id').value = ''; // Reset selection
        document.getElementById('rackStorageModal').classList.remove('hidden');
    }
    
    function submitReceive() {
        const orderId = document.getElementById('storage_order_id').value;
        const rackCode = document.getElementById('storage_rack_id').value;
        
        if (!rackCode) {
            Swal.fire('Error', 'Silakan pilih rak penyimpanan terlebih dahulu.', 'error');
            return;
        }

        const formId = `receive-mobile-${orderId}`;
        const form = document.getElementById(formId);
        
        if (!form) {
             Swal.fire('Error', 'Form tidak ditemukan.', 'error');
             return;
        }

        // Find the hidden rack input in the form
        const rackInput = form.querySelector('input[name="rack_code"]');
        if (rackInput) {
            rackInput.value = rackCode;
            form.submit();
        } else {
            Swal.fire('Error', 'Input rack code tidak ditemukan di form.', 'error');
        }
    }
</script>

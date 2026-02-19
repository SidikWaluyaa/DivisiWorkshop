{{-- JavaScript for Lead Detail View --}}
<script>
    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Edit Item Modal
    function openEditItemModal(itemId, btn) {
        const item = JSON.parse(btn.getAttribute('data-item'));
        const modal = document.getElementById('editItemModal');
        const form = document.getElementById('editItemForm');
        const warning = document.getElementById('lockedItemWarning');
        const reasonField = document.getElementById('item_revision_reason');
        
        // Reset form action
        form.action = `/cs/items/${itemId}`;
        
        // Populate fields
        document.getElementById('item_category').value = item.category || '';
        document.getElementById('item_brand').value = item.shoe_brand || '';
        document.getElementById('item_type').value = item.shoe_type || '';
        document.getElementById('item_size').value = item.shoe_size || '';
        document.getElementById('item_color').value = item.shoe_color || '';
        document.getElementById('item_notes').value = item.item_notes || '';
        
        // Populate services checkboxes & details
        const itemServices = item.services || [];
        const itemServiceIds = itemServices.filter(s => s.id).map(s => String(s.id));
        const itemServiceDetails = {};
        itemServices.forEach(s => { if(s.id) itemServiceDetails[s.id] = s.manual_detail || ''; });

        document.querySelectorAll('.service-edit-checkbox').forEach(cb => {
            const isChecked = itemServiceIds.includes(String(cb.value));
            cb.checked = isChecked;
            
            // Handle Detail Input
            const container = document.getElementById(`detail_container_${cb.value}`);
            const input = document.getElementById(`service_detail_${cb.value}`);
            if (isChecked) {
                container.classList.remove('hidden');
                input.value = itemServiceDetails[cb.value] || '';
            } else {
                container.classList.add('hidden');
                input.value = '';
            }
            cb.setAttribute('onchange', 'toggleEditItemServiceDetail(this); calculateEditTotal();');
        });

        // Handle Custom Services
        const customContainer = document.getElementById('edit_custom_services_container');
        customContainer.innerHTML = '';
        itemServices.forEach(s => {
            if (!s.id) {
                addCustomServiceRow(s.name, s.price, s.manual_detail);
            }
        });
        
        calculateEditTotal();
        
        // Check locking status
        const isLocked = @json(in_array($lead->status, ['CONVERTED', 'LOST']));
        
        if (isLocked) {
            warning.classList.remove('hidden');
            reasonField.setAttribute('required', 'required');
        } else {
            warning.classList.add('hidden');
            reasonField.removeAttribute('required');
        }
        
        modal.classList.remove('hidden');
    }

    function addCustomServiceRow(name = '', price = 0, detail = '') {
        const container = document.getElementById('edit_custom_services_container');
        const rowId = 'custom_' + Date.now() + Math.random().toString(36).substr(2, 5);
        
        const html = `
            <div id="${rowId}" class="bg-blue-50/50 p-2 rounded-lg border border-blue-100">
                <div class="grid grid-cols-12 gap-2 mb-2">
                    <div class="col-span-11 grid grid-cols-2 gap-2">
                        <input type="text" name="custom_service_names[]" value="${name}" required class="px-2 py-1 text-[10px] rounded border-gray-200" placeholder="Nama Layanan Kustom">
                        <input type="number" name="custom_service_prices[]" value="${price}" required class="px-2 py-1 text-[10px] rounded border-gray-200 custom-price-input" placeholder="Harga" oninput="calculateEditTotal()">
                    </div>
                    <div class="col-span-1 flex items-center justify-center">
                        <button type="button" onclick="document.getElementById('${rowId}').remove(); calculateEditTotal()" class="text-red-400 hover:text-red-600">×</button>
                    </div>
                </div>
                <input type="text" name="custom_service_descriptions[]" value="${detail}" class="w-full px-2 py-1 text-[10px] rounded border-gray-200 bg-white/50" placeholder="Keterangan kustom...">
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function calculateEditTotal() {
        let total = 0;
        const modal = document.getElementById('editItemModal');
        // From checkboxes
        modal.querySelectorAll('.service-edit-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.getAttribute('data-price')) || 0;
        });
        // From custom services
        modal.querySelectorAll('.custom-price-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('item_total_price').value = Math.round(total);
    }

    function toggleEditItemServiceDetail(cb) {
        const container = document.getElementById(`detail_container_${cb.value}`);
        const input = document.getElementById(`service_detail_${cb.value}`);
        if (cb.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            input.value = '';
        }
        calculateEditTotal();
    }

    function closeEditItemModal() {
        document.getElementById('editItemModal').classList.add('hidden');
    }

    // Activity Modal
    function openActivityModal() {
        document.getElementById('activityModal').classList.remove('hidden');
    }
    function closeActivityModal() {
        document.getElementById('activityModal').classList.add('hidden');
    }

    // Follow Up Modal
    function openFollowUpModal() {
        document.getElementById('followUpModal').classList.remove('hidden');
    }
    function closeFollowUpModal() {
        document.getElementById('followUpModal').classList.add('hidden');
    }

    // Quotation Modal
    function openQuotationModal() {
        document.getElementById('quotationModal').classList.remove('hidden');
    }
    function closeQuotationModal() {
        document.getElementById('quotationModal').classList.add('hidden');
    }

    function autoFillPrice(input) {
        const val = input.value;
        const options = document.getElementById('service-list').options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].value === val) {
                const price = options[i].getAttribute('data-price');
                const row = input.closest('.quotation-item');
                row.querySelector('.service-price-input').value = price;
                break;
            }
        }
    }

    let itemIndex = 1;
    function addQuotationItem() {
        const container = document.getElementById('quotation-items');
        const newItem = `
            <div class="quotation-item border rounded p-3 bg-gray-50">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-5 relative">
                        <input type="text" name="items[${itemIndex}][service_name]" list="service-list" required class="service-name-input w-full px-2 py-1 border rounded text-sm" placeholder="Nama Service" onchange="autoFillPrice(this)">
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="items[${itemIndex}][qty]" value="1" min="1" required class="w-full px-2 py-1 border rounded text-sm" placeholder="Qty">
                    </div>
                    <div class="col-span-4">
                        <input type="number" name="items[${itemIndex}][price]" required class="service-price-input w-full px-2 py-1 border rounded text-sm" placeholder="Harga">
                    </div>
                    <div class="col-span-1 flex items-center justify-center">
                        <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <input type="text" name="items[${itemIndex}][description]" class="w-full px-2 py-1 border rounded text-sm" placeholder="Deskripsi (opsional)">
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newItem);
        itemIndex++;
    }

    function removeItem(button) {
        const items = document.querySelectorAll('.quotation-item');
        if (items.length > 1) {
            button.closest('.quotation-item').remove();
        } else {
            alert('Minimal harus ada 1 item!');
        }
    }

    // Handover Modal
    function openHandoverModal() {
        document.getElementById('handoverModal').classList.remove('hidden');
    }
    function closeHandoverModal() {
        document.getElementById('handoverModal').classList.add('hidden');
    }

    // SPK Modal
    function openSpkModal() {
        document.getElementById('spkModal').classList.remove('hidden');
    }
    function closeSpkModal() {
        document.getElementById('spkModal').classList.add('hidden');
    }

    // Quick Actions
    function moveToKonsultasi() {
        const notes = prompt('Catatan untuk pindah ke Konsultasi:');
        if (notes !== null) {
            fetch("{{ route('cs.leads.move-konsultasi', $lead->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ notes: notes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    function moveToClosing() {
        if (confirm('Pindahkan lead ke Closing?')) {
            fetch("{{ route('cs.leads.move-closing', $lead->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    function moveToFollowUp() {
        if (confirm('Pindahkan lead ke Follow-up Konsultasi?')) {
            fetch("{{ route('cs.leads.move-follow-up', $lead->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ notes: 'Dipindahkan ke Follow-up Konsultasi' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    function backToKonsultasi() {
        const notes = prompt('Catatan untuk kembali ke Konsultasi:');
        if (notes !== null) {
            fetch("{{ route('cs.leads.update-status', $lead->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: 'KONSULTASI', notes: notes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    function rejectQuotation(quotationId) {
        const reason = prompt('Alasan penolakan:');
        if (reason !== null && reason.trim() !== '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/cs/quotations/${quotationId}/reject`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = reason;
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function markLost() {
        const reason = prompt('Alasan lead LOST:');
        if (reason !== null && reason.trim() !== '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('cs.leads.mark-lost', $lead->id) }}";
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'lost_reason';
            reasonInput.value = reason;
            
            form.appendChild(csrfInput);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // SPK Modal Helper Functions
    function filterServices(itemId) {
        const input = document.getElementById(`search-services-${itemId}`);
        const filter = input.value.toLowerCase();
        const wrappers = document.querySelectorAll(`[class^="service-wrapper-${itemId}-"]`);
        
        wrappers.forEach(wrapper => {
            const label = wrapper.querySelector('label');
            const name = label.getAttribute('data-service-name');
            const cat = label.getAttribute('data-service-category');
            if (name.includes(filter) || cat.includes(filter)) {
                wrapper.classList.remove('hidden');
            } else {
                wrapper.classList.add('hidden');
            }
        });
    }

    function toggleServiceDetail(itemId, serviceId) {
        const cb = document.getElementById(`service-${itemId}-${serviceId}`);
        const detail = document.getElementById(`detail-${itemId}-${serviceId}`);
        if (cb.checked) {
            detail.classList.remove('hidden');
        } else {
            detail.classList.add('hidden');
            const input = document.getElementById(`detail-input-${itemId}-${serviceId}`);
            if(input) input.value = '';
        }
    }

    function updateItemTotal(itemId) {
        let total = 0;
        const modal = document.getElementById('spkModal');
        modal.querySelectorAll(`.service-checkbox[data-item-id="${itemId}"]:checked`).forEach(cb => {
            total += parseFloat(cb.getAttribute('data-price')) || 0;
        });

        const customItems = document.querySelectorAll(`#selected-list-${itemId} .custom-item-row`);
        customItems.forEach(row => {
            total += parseFloat(row.getAttribute('data-price')) || 0;
        });

        document.getElementById(`item-subtotal-${itemId}`).innerText = 'Rp ' + Math.round(total).toLocaleString('id-ID');
        updateGrandTotal();
    }

    function updateSelectedServices(itemId) {
        const list = document.getElementById(`selected-list-${itemId}`);
        const summaryContainer = document.getElementById(`selected-summary-${itemId}`);
        const checkedBoxes = document.querySelectorAll(`.service-checkbox[data-item-id="${itemId}"]:checked`);
        const customRows = list.querySelectorAll('.custom-item-row');
        
        if (checkedBoxes.length === 0 && customRows.length === 0) {
            summaryContainer.classList.add('hidden');
            return;
        }
        summaryContainer.classList.remove('hidden');
        
        // Collect custom rows HTML to preserve them
        let customHtml = '';
        customRows.forEach(row => {
            customHtml += row.outerHTML;
        });

        let html = '';
        checkedBoxes.forEach(cb => {
            const serviceId = cb.value;
            const name = cb.getAttribute('data-name');
            const price = parseFloat(cb.getAttribute('data-price')).toLocaleString('id-ID');
            const detailInput = document.getElementById(`detail-input-${itemId}-${serviceId}`);
            const detail = detailInput ? detailInput.value : '';
            
            html += `
                <div class="flex justify-between items-start p-3 bg-white/5 rounded-xl border border-gray-800">
                    <div>
                        <p class="text-[10px] font-black text-white uppercase">${name}</p>
                        ${detail ? `<p class="text-[9px] text-[#22AF85] italic mt-1">"${detail}"</p>` : ''}
                    </div>
                    <span class="text-[10px] font-black text-gray-400">Rp ${price}</span>
                </div>
            `;
        });
        
        list.innerHTML = html + customHtml;
    }

    function toggleCustomService(itemId) {
        const div = document.getElementById(`custom-service-${itemId}`);
        div.classList.toggle('hidden');
    }

    function addCustomService(itemId) {
        const nameInput = document.getElementById(`custom-name-${itemId}`);
        const priceInput = document.getElementById(`custom-price-${itemId}`);
        const catSelect = document.getElementById(`custom-category-${itemId}`);
        const descInput = document.getElementById(`custom-description-${itemId}`);
        
        const name = nameInput.value.trim();
        const price = parseFloat(priceInput.value) || 0;
        const category = catSelect.value;
        const desc = descInput.value.trim();
        
        if (!name || price <= 0 || !category) {
            alert('Nama, Harga, dan Kategori layanan kustom harus diisi!');
            return;
        }
        
        const list = document.getElementById(`selected-list-${itemId}`);
        const summaryContainer = document.getElementById(`selected-summary-${itemId}`);
        summaryContainer.classList.remove('hidden');
        
        const rowId = 'cs_' + Date.now();
        const rowHtml = `
            <div id="${rowId}" class="custom-item-row flex justify-between items-start p-3 bg-[#22AF85]/10 rounded-xl border border-[#22AF85]/20 group/row" data-price="${price}">
                <input type="hidden" name="items[${itemId}][custom_services][${rowId}][name]" value="${name}">
                <input type="hidden" name="items[${itemId}][custom_services][${rowId}][price]" value="${price}">
                <input type="hidden" name="items[${itemId}][custom_services][${rowId}][category]" value="${category}">
                <input type="hidden" name="items[${itemId}][custom_services][${rowId}][description]" value="${desc}">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                        <p class="text-[10px] font-black text-white uppercase">${name}</p>
                    </div>
                    <p class="text-[9px] text-[#FFC232] font-black uppercase mt-1 tracking-widest">${category}</p>
                    ${desc ? `<p class="text-[9px] text-[#22AF85] italic mt-1">"${desc}"</p>` : ''}
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black text-[#22AF85]">Rp ${price.toLocaleString('id-ID')}</span>
                    <button type="button" onclick="document.getElementById('${rowId}').remove(); updateItemTotal(${itemId});" 
                            class="text-gray-500 hover:text-red-400 transition font-black text-lg">×</button>
                </div>
            </div>
        `;
        
        list.insertAdjacentHTML('beforeend', rowHtml);
        
        nameInput.value = '';
        priceInput.value = '';
        catSelect.value = '';
        descInput.value = '';
        document.getElementById(`custom-service-${itemId}`).classList.add('hidden');
        
        updateItemTotal(itemId);
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        const modal = document.getElementById('spkModal');
        modal.querySelectorAll('.service-checkbox:checked').forEach(cb => {
            grandTotal += parseFloat(cb.getAttribute('data-price')) || 0;
        });
        
        modal.querySelectorAll('.custom-item-row').forEach(row => {
            grandTotal += parseFloat(row.getAttribute('data-price')) || 0;
        });
        
        document.getElementById('grand-total').innerText = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');
        
        const dpSuggestion = Math.ceil(grandTotal * 0.3);
        document.getElementById('dp-suggestion').innerText = 'Rp ' + dpSuggestion.toLocaleString('id-ID');
        
        const dpInput = document.getElementById('dp-amount-input');
        if (parseFloat(dpInput.value) <= 0) {
            dpInput.value = dpSuggestion;
        }
    }

    // SPK Preview Logic
    function updateSpkPreview() {
        const deliverySelect = document.getElementById('deliveryTypeSelect');
        if (!deliverySelect) return;
        const deliveryCode = deliverySelect.options[deliverySelect.selectedIndex].getAttribute('data-code');
        const dateStr = "{{ date('ym-d') }}";
        const csCode = document.getElementById('manualCsInput').value.toUpperCase() || '??';
        
        const previewText = `${deliveryCode}-${dateStr}-XXXX-${csCode}`;
        document.getElementById('spkPreview').innerText = previewText;
    }

    // Promo Validation Logic
    async function validatePromo() {
        const codeInput = document.getElementById('promo-code-input');
        if (!codeInput) return;
        const code = codeInput.value.trim();
        const statusDiv = document.getElementById('promo-status');
        const messageP = document.getElementById('promo-message');
        const btnApply = document.getElementById('btn-apply-promo');

        if (!code) {
            alert('Masukkan kode promo!');
            return;
        }

        btnApply.disabled = true;
        btnApply.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Checking...';
        statusDiv.classList.add('hidden');

        try {
            const checkboxes = document.querySelectorAll('.service-checkbox:checked');
            let subtotal = 0;
            let serviceIds = [];
            checkboxes.forEach(cb => {
                subtotal += parseFloat(cb.dataset.price) || 0;
                if (cb.dataset.serviceId) {
                    serviceIds.push(cb.dataset.serviceId);
                }
            });

            if (subtotal <= 0) {
                alert('Pilih setidaknya satu layanan terlebih dahulu!');
                btnApply.disabled = false;
                btnApply.innerText = 'Apply';
                return;
            }

            const response = await fetch('/api/cs/promos/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    total_amount: subtotal,
                    service_ids: serviceIds
                })
            });

            const result = await response.json();
            statusDiv.classList.remove('hidden');
            if (result.valid) {
                messageP.className = 'text-xs font-semibold text-green-600';
                messageP.innerHTML = `✅ Promo Berhasil! Diskon: <strong>Rp ${result.discount.toLocaleString('id-ID')}</strong>`;
                codeInput.classList.add('border-green-500');
                codeInput.classList.remove('border-red-500');
                const grandTotal = subtotal - result.discount;
                document.getElementById('grand-total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
                const dpSuggestion = Math.ceil(grandTotal * 0.3);
                document.getElementById('dp-suggestion').textContent = 'Saran (30%): Rp ' + dpSuggestion.toLocaleString('id-ID');
                document.getElementById('dp-amount-input').value = dpSuggestion;
            } else {
                messageP.className = 'text-xs font-semibold text-red-600';
                messageP.innerText = '❌ ' + result.message;
                codeInput.classList.add('border-red-500');
                codeInput.classList.remove('border-green-500');
                document.getElementById('grand-total').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            }
        } catch (error) {
            console.error('Error validating promo:', error);
            alert('Gagal memvalidasi promo. Silakan coba lagi.');
        } finally {
            btnApply.disabled = false;
            btnApply.innerText = 'Apply';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('deliveryTypeSelect')) {
            updateSpkPreview();
        }
    });
</script>

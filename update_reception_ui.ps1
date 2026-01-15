# Script untuk Update Reception Index dengan Fitur Email
# Jalankan di PowerShell: .\update_reception_ui.ps1

$filePath = "c:\laragon\www\SistemWorkshop\resources\views\reception\index.blade.php"
$backupPath = "c:\laragon\www\SistemWorkshop\resources\views\reception\index.blade.php.backup2"

# Backup file
Write-Host "Creating backup..." -ForegroundColor Yellow
Copy-Item $filePath $backupPath -Force
Write-Host "Backup created: $backupPath" -ForegroundColor Green

# Read file content
$content = Get-Content $filePath -Raw

# 1. Update Customer Column - Add email display
Write-Host "`nUpdating customer column..." -ForegroundColor Yellow
$oldCustomerColumn = @'
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-800">{{ $order->customer_name }}</span>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-xs text-green-600 hover:text-green-800 flex items-center gap-1 mt-0.5 w-fit hover:underline">
'@

$newCustomerColumn = @'
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <span class="font-bold text-gray-800">{{ $order->customer_name }}</span>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-xs text-green-600 hover:text-green-800 flex items-center gap-1 w-fit hover:underline">
'@

if ($content -match [regex]::Escape($oldCustomerColumn)) {
    $content = $content -replace [regex]::Escape($oldCustomerColumn), $newCustomerColumn
    Write-Host "✓ Customer column header updated" -ForegroundColor Green
}

# Add email display after phone number
$oldPhoneEnd = @'
                                                    {{ $order->customer_phone }}
                                                </a>
                                            </div>
                                        </td>
'@

$newPhoneEnd = @'
                                                    {{ $order->customer_phone }}
                                                </a>
                                                
                                                {{-- Email Status & Edit --}}
                                                <div class="email-container-{{ $order->id }}">
                                                    @if($order->customer_email)
                                                        <div class="flex items-center gap-1 text-xs text-blue-600">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                            <span class="truncate max-w-[150px]" title="{{ $order->customer_email }}">{{ $order->customer_email }}</span>
                                                            <button type="button" onclick="openEditEmailModal('{{ $order->id }}', '{{ $order->customer_email }}')" class="text-gray-400 hover:text-teal-600 transition-colors" title="Edit Email">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button type="button" onclick="openEditEmailModal('{{ $order->id }}', '')" class="flex items-center gap-1 text-xs text-gray-400 hover:text-teal-600 transition-colors w-fit">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                            Tambah Email
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
'@

if ($content -match [regex]::Escape($oldPhoneEnd)) {
    $content = $content -replace [regex]::Escape($oldPhoneEnd), $newPhoneEnd
    Write-Host "✓ Email display added" -ForegroundColor Green
}

# 2. Update Email Button to be conditional
Write-Host "`nUpdating email button..." -ForegroundColor Yellow
$oldEmailButton = @'
                                                {{-- SMTP Email Trigger --}}
                                                <button type="button" onclick="sendEmailNotification('{{ $order->id }}')" class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors inline-block" title="Kirim Nota Digital via Email">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
'@

$newEmailButton = @'
                                                {{-- SMTP Email Trigger - Only show if email exists --}}
                                                @if($order->customer_email)
                                                    <button type="button" onclick="sendEmailNotification('{{ $order->id }}')" class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors inline-block" title="Kirim Nota Digital via Email">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button type="button" onclick="Swal.fire('Email Tidak Ada', 'Silakan tambahkan email customer terlebih dahulu.', 'warning')" class="p-2 text-gray-300 cursor-not-allowed rounded-lg" title="Email customer belum tersedia" disabled>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                @endif
'@

if ($content -match [regex]::Escape($oldEmailButton)) {
    $content = $content -replace [regex]::Escape($oldEmailButton), $newEmailButton
    Write-Host "✓ Email button made conditional" -ForegroundColor Green
}

# 3. Add Modal and JavaScript before </x-app-layout>
Write-Host "`nAdding modal and JavaScript..." -ForegroundColor Yellow
$modalScript = @'

    {{-- Edit Email Modal --}}
    <div id="editEmailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Email Customer</h3>
                <form id="editEmailForm" onsubmit="updateEmail(event)">
                    <input type="hidden" id="editOrderId" value="">
                    <div class="mb-4">
                        <label for="editEmailInput" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="editEmailInput" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500" 
                               placeholder="customer@example.com">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika ingin menghapus email</p>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="closeEditEmailModal()" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                closeEditEmailModal();
                
                // Reload page after 2 seconds to update UI
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan: ' + error.message
            });
        });
    }

    // Close modal when clicking outside
    document.getElementById('editEmailModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditEmailModal();
        }
    });
    </script>
</x-app-layout>
'@

if ($content -match '</x-app-layout>') {
    $content = $content -replace '</x-app-layout>', $modalScript
    Write-Host "✓ Modal and JavaScript added" -ForegroundColor Green
}

# Save updated content
Set-Content -Path $filePath -Value $content -NoNewline
Write-Host "`n✅ File updated successfully!" -ForegroundColor Green
Write-Host "Backup tersimpan di: $backupPath" -ForegroundColor Cyan
Write-Host "`nSilakan refresh browser untuk melihat perubahan." -ForegroundColor Yellow

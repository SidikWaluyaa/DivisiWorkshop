<div x-data="{ 
    showEditModal: false,
    issue: {},
    availableServices: [],
    loading: false,
    init() {
        window.addEventListener('open-edit-issue-modal', (e) => {
            this.issue = e.detail.issue;
            this.availableServices = e.detail.availableServices || [];
            this.showEditModal = true;
        });
    },
    async updateIssue() {
        this.loading = true;
        try {
            const response = await fetch(`/cx-issues/${this.issue.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                },
                body: JSON.stringify(this.issue)
            });
            const result = await response.json();
            if (result.status === 'success') {
                window.location.reload();
            } else {
                alert('Update failed: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('An error occurred.');
        } finally {
            this.loading = false;
        }
    }
}" x-show="showEditModal" 
class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showEditModal = false"></div>

        <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-6 py-6 ring-1 ring-gray-900/5">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Edit Kendala (QC Reject)</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">SPK Number</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="issue.spk_number"></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Upper Issue</label>
                            <textarea x-model="issue.desc_upper" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="2"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Sol Issue</label>
                            <textarea x-model="issue.desc_sol" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="2"></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Kondisi Bawaan</label>
                        <textarea x-model="issue.desc_kondisi_bawaan" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="2"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Recommended Services</label>
                            <select x-model="issue.rec_service_1" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">None</option>
                                <template x-for="service in availableServices" :key="service.id">
                                    <option :value="service.name" x-text="service.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Suggested Services</label>
                            <select x-model="issue.sug_service_1" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">None</option>
                                <template x-for="service in availableServices" :key="service.id">
                                    <option :value="service.name" x-text="service.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">Cancel</button>
                    <button @click="updateIssue()" :disabled="loading" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 disabled:opacity-50 flex items-center">
                        <template x-if="loading">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        Update Kendala
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

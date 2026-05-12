<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header Skeleton --}}
        <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 animate-pulse">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="h-8 w-48 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-4 w-64 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                </div>
                <div class="flex gap-4">
                    <div class="h-10 w-64 bg-gray-100 dark:bg-gray-700 rounded-xl"></div>
                    <div class="h-10 w-64 bg-gray-100 dark:bg-gray-700 rounded-xl"></div>
                </div>
            </div>
        </div>

        {{-- Grid Skeleton (2 Columns for Split View) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @for($i = 0; $i < 4; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden animate-pulse">
                    {{-- Card Header --}}
                    <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl"></div>
                            <div class="space-y-2">
                                <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                <div class="h-3 w-32 bg-gray-100 dark:bg-gray-700 rounded"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Card Body (Split View) --}}
                    <div class="flex h-72 sm:h-96">
                        <div class="w-1/2 bg-gray-100 dark:bg-gray-700 border-r border-white dark:border-gray-900"></div>
                        <div class="w-1/2 bg-gray-50 dark:bg-gray-800"></div>
                    </div>
                    {{-- Card Footer --}}
                    <div class="px-6 py-4 bg-gray-50/30 dark:bg-gray-800/30 flex justify-between">
                        <div class="h-3 w-20 bg-gray-100 dark:bg-gray-700 rounded"></div>
                        <div class="flex gap-2">
                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

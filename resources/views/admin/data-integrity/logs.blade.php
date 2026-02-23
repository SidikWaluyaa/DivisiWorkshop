@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <nav class="flex mb-3" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('admin.data-integrity.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">Data Integrity</a></li>
                    <li><svg class="w-2 h-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                    <li><span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Log Viewer</span></li>
                </ol>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Log Sistem <span class="text-red-600">Terbaru</span></h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Menampilkan 200 baris terakhir aktivitas sistem.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.data-integrity.logs.clear') }}" method="POST" onsubmit="return confirm('Hapus semua isi log? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-white border border-gray-200 text-red-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-50 hover:border-red-100 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Bersihkan Log
                </button>
            </form>
            <a href="{{ route('admin.data-integrity.logs') }}" class="px-5 py-2.5 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Refresh
            </a>
        </div>
    </div>

    <!-- Alert Success/Error -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-2xl flex items-center gap-3">
            <div class="p-2 bg-green-100 text-green-600 rounded-xl">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
            </div>
            <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Log Console -->
    <div class="bg-gray-900 rounded-3xl overflow-hidden border border-gray-800 shadow-2xl">
        <div class="px-6 py-4 bg-gray-800/50 border-b border-gray-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-red-500/20 border border-red-500/40"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/20 border border-yellow-500/40"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/20 border border-green-500/40"></div>
                </div>
                <span class="ml-2 text-[10px] font-black text-gray-500 uppercase tracking-widest">laravel.log</span>
            </div>
            <div class="text-[9px] font-black text-gray-500 uppercase tracking-widest">
                {{ count($lines) }} Entries Found
            </div>
        </div>

        <div class="p-6 overflow-x-auto">
            <div class="font-mono text-sm leading-relaxed min-w-max">
                @forelse($lines as $line)
                    @php
                        $isError = str_contains($line, 'ERROR') || str_contains($line, 'exception');
                        $isSuccess = str_contains($line, 'Repair Tool Success');
                        $isInfo = str_contains($line, 'INFO');
                        $time = '';
                        $message = $line;
                        
                        if (preg_match('/^\[(.*?)\] (.*?): (.*)$/', $line, $matches)) {
                            $time = $matches[1];
                            $level = $matches[2];
                            $message = $matches[3];
                        }
                    @endphp
                    <div class="py-1 flex gap-4 hover:bg-gray-800/30 transition-colors group">
                        <span class="text-gray-600 select-none w-24 shrink-0 text-[10px]">{{ $time }}</span>
                        <span class="shrink-0 font-bold uppercase text-[10px] w-16 {{ $isError ? 'text-red-400' : ($isSuccess ? 'text-green-400' : ($isInfo ? 'text-blue-400' : 'text-gray-500')) }}">
                            {{ $isError ? 'ERROR' : ($isSuccess ? 'SUCCESS' : ($isInfo ? 'INFO' : 'DEBUG')) }}
                        </span>
                        <span class="{{ $isError ? 'text-red-300' : ($isSuccess ? 'text-green-300' : 'text-gray-300') }} break-all">
                            {{ $message }}
                        </span>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <svg class="w-12 h-12 text-gray-800 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-gray-600 font-bold uppercase tracking-widest text-[10px]">Log Kosong</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for Dark Console */
    .bg-gray-900::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .bg-gray-900::-webkit-scrollbar-track {
        background: #111827;
    }
    .bg-gray-900::-webkit-scrollbar-thumb {
        background: #374151;
        border-radius: 4px;
    }
    .bg-gray-900::-webkit-scrollbar-thumb:hover {
        background: #4b5563;
    }
</style>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Executive Dashboard</h2>
        <style>
            :root {
                --brand-green: #22AF85;
                --brand-yellow: #FFC232;
                --brand-dark: #1a1a2e;
            }
            .text-brand-green { color: var(--brand-green); }
            .bg-brand-green { background-color: var(--brand-green); }
            .bg-brand-yellow { background-color: var(--brand-yellow); }
            .border-brand-green { border-color: var(--brand-green); }

            .stat-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
            }
            @keyframes fade-in-up {
                from { opacity: 0; transform: translateY(16px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fade-in-up 0.6s ease-out both;
            }
            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
            .delay-400 { animation-delay: 0.4s; }
            .delay-500 { animation-delay: 0.5s; }

            @keyframes pulse-soft {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.05); opacity: 0.8; }
            }
            .animate-pulse-soft { animation: pulse-soft 3s infinite ease-in-out; }

            .urgent-glow {
                animation: urgent-glow 2s infinite;
            }
            @keyframes urgent-glow {
                0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.15); }
                50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            }
        </style>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

            {{-- Section 1: Premium Header --}}
            @include('dashboard-v2.sections.header')

            {{-- Section 2: KPI Metrics Row --}}
            @include('dashboard-v2.sections.kpi-cards')

            {{-- Section 3: Customer Journey Pipeline --}}
            @include('dashboard-v2.sections.journey-map')

            {{-- Section 4: Charts Row — Production + Revenue --}}
            @include('dashboard-v2.sections.production')

            {{-- Section 5: Business Intelligence --}}
            @include('dashboard-v2.sections.business-intel')

            {{-- Section 6: Urgent Actions + Quick Actions --}}
            @include('dashboard-v2.sections.urgent-actions')

        </div>
    </div>

    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Charts + Real-Time Polling --}}
    @include('dashboard-v2.sections.charts-script')

</x-app-layout>

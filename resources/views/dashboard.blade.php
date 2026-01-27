<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Analytics') }}
        </h2>
        
        <!-- ApexCharts CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
        
        <style>
            [x-cloak] { display: none !important; }
            
            /* Premium Dashboard Styles */
            .section-gradient {
                background: linear-gradient(135deg, rgba(20, 184, 166, 0.03) 0%, rgba(249, 115, 22, 0.03) 100%);
            }
            
            .chart-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .chart-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            }
            
            .section-divider {
                height: 2px;
                background: linear-gradient(90deg, transparent, rgba(20, 184, 166, 0.3), rgba(249, 115, 22, 0.3), transparent);
            }
            
            .metric-card {
                transition: all 0.3s ease;
            }
            
            .metric-card:hover {
                transform: translateY(-2px) scale(1.02);
            }
            
            /* ApexCharts Custom Styling */
            .apexcharts-tooltip {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(20, 184, 166, 0.2) !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
            }
            
            .apexcharts-tooltip-title {
                background: linear-gradient(135deg, #14b8a6, #f97316) !important;
                color: white !important;
                font-weight: 700 !important;
            }
            
            /* Smooth Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease-out;
            }
            
            /* Section Header Glow */
            .section-icon-glow {
                box-shadow: 0 0 20px rgba(20, 184, 166, 0.3);
            }
        </style>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @include('dashboard.partials.header')
            
            @include('dashboard.partials.filters')

            @include('dashboard.partials.metrics')

            @include('dashboard.partials.logistics')

            @include('dashboard.partials.operational')

            @include('dashboard.partials.business-intelligence')

            @include('dashboard.partials.inventory')

        </div>
    </div>

        </div>
    </div>

    {{-- Chart.js Local --}}
    <script src="{{ asset('js/vendor/chart.min.js') }}"></script>
    
    <script>
        // Chart colors
        const colors = {
            primary: '#22AF85', // Brand Green
            success: '#22AF85',
            warning: '#FFC232', // Brand Yellow
            danger: '#ef4444',
            info: '#3b82f6',
            purple: '#8b5cf6',
            pink: '#ec4899',
            teal: '#22AF85',
        };

        const statusColors = {
            'DITERIMA': '#3b82f6',
            'ASSESSMENT': '#FFC232',
            'PREPARATION': '#06b6d4',
            'SORTIR': '#6366f1',
            'PRODUCTION': '#FFC232',
            'QC': '#22AF85',
            'SELESAI': '#22AF85',
        };

        // Status Distribution Donut Chart (ApexCharts) - Premium Interactive
        const statusOptions = {
            chart: {
                type: 'donut',
                height: 250,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: @json($statusDistribution['labels']).map(status => statusColors[status] || colors.primary),
            series: @json($statusDistribution['data']),
            labels: @json($statusDistribution['labels']),
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Orders',
                                fontSize: '14px',
                                fontWeight: 700,
                                color: '#374151',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            },
                            value: {
                                fontSize: '24px',
                                fontWeight: 900,
                                color: '#22AF85'
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 600,
                markers: {
                    width: 10,
                    height: 10,
                    radius: 2
                }
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(value) {
                        return value + ' orders'
                    }
                }
            }
        };
        
        const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
        statusChart.render();

        // Daily Trends Area Chart (ApexCharts) - Yellow/Orange Gradient
        const trendsOptions = {
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#FFC232'], // Brand Yellow
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                colors: ['#FFC232'],
                strokeWidth: 2,
                strokeColors: '#fff',
                hover: {
                    size: 6
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Orders',
                data: @json($dailyTrends['data'])
            }],
            xaxis: {
                categories: @json($dailyTrends['labels']),
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                },
                min: 0
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                xaxis: {
                    lines: { show: false }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(value) {
                        return value + ' orders'
                    }
                }
            }
        };
        
        const trendsChart = new ApexCharts(document.querySelector("#trendsChart"), trendsOptions);
        trendsChart.render();



        // Bottleneck Analysis (ApexCharts) - Horizontal Bar
        const bottleneckObj = @json($processAnalytics);
        const bottleneckData = Object.values(bottleneckObj); // Convert object to array
        const bottleneckLabels = bottleneckData.map(item => item.label);
        const bottleneckValues = bottleneckData.map(item => item.avgTime);

        const bottleneckOptions = {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                animations: { enabled: true }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '60%',
                    distributed: true
                }
            },
            colors: [colors.primary, colors.purple, colors.warning, colors.success, colors.danger, colors.info],
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: { colors: ['#fff'], fontSize: '12px', fontWeight: 'bold' },
                formatter: function (val, opt) {
                    return val + " Jam"
                },
                offsetX: 0,
            },
            series: [{
                name: 'Rata-rata Durasi',
                data: bottleneckValues
            }],
            xaxis: {
                categories: bottleneckLabels,
                labels: {
                    style: { fontSize: '12px', fontWeight: 600 },
                    formatter: function(val) { return val + " Jam" }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) { return val + " Jam" }
                }
            },
            legend: { show: false },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
            }
        };

        if (document.querySelector("#bottleneckChart")) {
            new ApexCharts(document.querySelector("#bottleneckChart"), bottleneckOptions).render();
        }


        // Revenue Area Chart (ApexCharts) - Premium Gradient
        const revenueOptions = {
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#22AF85'], // Brand Green
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Pendapatan',
                data: @json($revenueData['daily']['data'])
            }],
            xaxis: {
                categories: @json($revenueData['daily']['labels']),
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    formatter: function(value) {
                        return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                    }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                xaxis: {
                    lines: { show: false }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        };
        
        const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
        revenueChart.render();


        // Material Trends Bar Chart
        new Chart(document.getElementById('materialTrendsChart'), {
            type: 'bar',
            data: {
                labels: @json($materialTrends['labels']),
                datasets: [{
                    label: 'Penggunaan',
                    data: @json($materialTrends['data']),
                    backgroundColor: colors.purple,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Service Trends Line Chart (Multi-line)
        const serviceTrendsData = @json($serviceTrends);
        const serviceColors = [colors.primary, colors.success, colors.warning];
        
        new Chart(document.getElementById('serviceTrendsChart'), {
            type: 'line',
            data: {
                labels: serviceTrendsData.labels,
                datasets: serviceTrendsData.datasets.map((dataset, index) => ({
                    label: dataset.label,
                    data: dataset.data,
                    borderColor: serviceColors[index] || colors.primary,
                    backgroundColor: (serviceColors[index] || colors.primary) + '20',
                    tension: 0.4,
                    fill: false
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        // Supplier Spend Bar Chart
        new Chart(document.getElementById('supplierSpendChart'), {
            type: 'bar',
            data: {
                labels: @json($supplierAnalytics['bySpend']['labels']),
                datasets: [{
                    label: 'Total Belanja (Rp)',
                    data: @json($supplierAnalytics['bySpend']['data']),
                    backgroundColor: colors.pink,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Supplier Rating Bar Chart
        new Chart(document.getElementById('supplierRatingChart'), {
            type: 'bar',
            data: {
                labels: @json($supplierAnalytics['byRating']['labels']),
                datasets: [{
                    label: 'Rating (1-5)',
                    data: @json($supplierAnalytics['byRating']['data']),
                    backgroundColor: colors.warning,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Material Category Pie Chart
        new Chart(document.getElementById('materialCategoryChart'), {
            type: 'pie',
            data: {
                labels: @json($materialCategoryStats['labels']),
                datasets: [{
                    data: @json($materialCategoryStats['data']),
                    backgroundColor: [colors.purple, colors.pink, colors.info, colors.warning],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });



        // Complaint Category Chart
        new Chart(document.getElementById('complaintCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: @json($complaintAnalytics['category_counts']['labels']),
                datasets: [{
                    data: @json($complaintAnalytics['category_counts']['data']),
                    backgroundColor: [colors.primary, colors.danger, colors.warning, colors.success, colors.info],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</x-app-layout>

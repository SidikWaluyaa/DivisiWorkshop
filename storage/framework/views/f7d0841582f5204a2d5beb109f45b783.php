<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <?php echo e(__('Dashboard Analytics')); ?>

        </h2>
        
        <!-- ApexCharts CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
        
        <style>
            [x-cloak] { display: none !important; }
            
            /* Premium Dashboard Styles */
            .section-gradient {
                background: linear-gradient(135deg, rgba(34, 175, 133, 0.03) 0%, rgba(255, 194, 50, 0.03) 100%);
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
                background: linear-gradient(135deg, #22AF85, #FFC232) !important;
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
     <?php $__env->endSlot(); ?>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <?php echo $__env->make('dashboard.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
            <?php echo $__env->make('dashboard.partials.filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('dashboard.partials.metrics', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('dashboard.partials.logistics', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('dashboard.partials.operational', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('dashboard.partials.business-intelligence', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('dashboard.partials.inventory', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>
    </div>

        </div>
    </div>

    
    <script src="<?php echo e(asset('js/vendor/chart.min.js')); ?>"></script>
    
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
            'PREPARATION': '#0ea5e9',
            'SORTIR': '#6366f1',
            'PRODUCTION': '#f97316',
            'QC': '#22AF85',
            'SELESAI': '#22AF85',
            'TERKIRIM': '#10b981',
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
            colors: <?php echo json_encode($statusDistribution['labels'], 15, 512) ?>.map(status => statusColors[status] || colors.primary),
            series: <?php echo json_encode($statusDistribution['data'], 15, 512) ?>,
            labels: <?php echo json_encode($statusDistribution['labels'], 15, 512) ?>,
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
                data: <?php echo json_encode($dailyTrends['data'], 15, 512) ?>
            }],
            xaxis: {
                categories: <?php echo json_encode($dailyTrends['labels'], 15, 512) ?>,
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
        const bottleneckObj = <?php echo json_encode($processAnalytics, 15, 512) ?>;
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
                data: <?php echo json_encode($revenueData['daily']['data'], 15, 512) ?>
            }],
            xaxis: {
                categories: <?php echo json_encode($revenueData['daily']['labels'], 15, 512) ?>,
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
                labels: <?php echo json_encode($materialTrends['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Penggunaan',
                    data: <?php echo json_encode($materialTrends['data'], 15, 512) ?>,
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
        const serviceTrendsData = <?php echo json_encode($serviceTrends, 15, 512) ?>;
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
                labels: <?php echo json_encode($supplierAnalytics['bySpend']['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Total Belanja (Rp)',
                    data: <?php echo json_encode($supplierAnalytics['bySpend']['data'], 15, 512) ?>,
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
                labels: <?php echo json_encode($supplierAnalytics['byRating']['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Rating (1-5)',
                    data: <?php echo json_encode($supplierAnalytics['byRating']['data'], 15, 512) ?>,
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
                labels: <?php echo json_encode($materialCategoryStats['labels'], 15, 512) ?>,
                datasets: [{
                    data: <?php echo json_encode($materialCategoryStats['data'], 15, 512) ?>,
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
                labels: <?php echo json_encode($complaintAnalytics['category_counts']['labels'], 15, 512) ?>,
                datasets: [{
                    data: <?php echo json_encode($complaintAnalytics['category_counts']['data'], 15, 512) ?>,
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard.blade.php ENDPATH**/ ?>
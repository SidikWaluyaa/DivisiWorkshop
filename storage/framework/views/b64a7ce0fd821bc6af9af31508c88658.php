
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ========== Chart Instances (stored globally for updates) ==========
    let funnelChart, revenueChart;

    // === Production Funnel Chart ===
    const funnelData = <?php echo json_encode($production['funnel'], 15, 512) ?>;

    funnelChart = new ApexCharts(document.querySelector("#productionFunnelChart"), {
        chart: {
            type: 'bar',
            height: 280,
            toolbar: { show: false },
            fontFamily: 'Inter, system-ui, sans-serif',
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 8,
                barHeight: '55%',
                distributed: true,
            }
        },
        colors: funnelData.map(d => d.color),
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: { colors: ['#fff'], fontSize: '12px', fontWeight: 800 },
            formatter: (val) => val + ' SPK',
            offsetX: 5,
        },
        series: [{
            name: 'Jumlah',
            data: funnelData.map(d => d.count),
        }],
        xaxis: {
            categories: funnelData.map(d => d.label),
            labels: { style: { fontSize: '11px', fontWeight: 600 } },
        },
        yaxis: {
            labels: { style: { fontSize: '11px', fontWeight: 700 } },
        },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        tooltip: {
            theme: 'light',
            y: { formatter: (val) => val + ' SPK' },
        },
        legend: { show: false },
    });
    funnelChart.render();

    // === Revenue Area Chart ===
    revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
        chart: {
            type: 'area',
            height: 260,
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: { enabled: true, easing: 'easeinout', speed: 800 },
        },
        colors: ['#22AF85'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.6,
                opacityTo: 0.05,
                stops: [0, 90, 100],
            },
        },
        stroke: { curve: 'smooth', width: 3 },
        markers: {
            size: 3,
            colors: ['#22AF85'],
            strokeWidth: 2,
            strokeColors: '#fff',
            hover: { size: 6 },
        },
        dataLabels: { enabled: false },
        series: [{
            name: 'Pendapatan',
            data: <?php echo json_encode($businessIntel['revenue']['data'], 15, 512) ?>,
        }],
        xaxis: {
            categories: <?php echo json_encode($businessIntel['revenue']['labels'], 15, 512) ?>,
            labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 },
                formatter: (value) => 'Rp ' + (value / 1000).toFixed(0) + 'k',
            },
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
        },
        tooltip: {
            theme: 'light',
            y: { formatter: (value) => 'Rp ' + value.toLocaleString('id-ID') },
        },
    });
    revenueChart.render();

    // ========== REAL-TIME POLLING ==========
    const POLL_INTERVAL = 30000; // 30 seconds
    const API_URL = <?php echo json_encode(route('dashboard.api-data'), 15, 512) ?>;
    let lastUpdate = Date.now();

    // Status indicator
    const pulseEl = document.getElementById('realtime-pulse');
    const lastUpdateEl = document.getElementById('last-update-time');

    async function fetchDashboardData() {
        try {
            const params = new URLSearchParams(window.location.search);
            const response = await fetch(API_URL + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Network error');
            const data = await response.json();

            updateKpiCards(data.kpi);
            updateJourney(data.journey);
            updateFunnelChart(data.production.funnel);
            updateTechnicians(data.production.technicians);
            updateRevenueChart(data.businessIntel.revenue);
            updateTopIssues(data.businessIntel.topIssues);
            updateComplaints(data.businessIntel.complaints);
            updateUrgentActions(data.urgentActions);
            updateQuickStats(data.quickStats);

            // Update clock
            if (data.serverTime) {
                const clockEl = document.getElementById('server-clock');
                if (clockEl) clockEl.textContent = data.serverTime;
            }

            // Flash pulse green
            lastUpdate = Date.now();
            if (pulseEl) pulseEl.classList.remove('bg-red-400');
            if (pulseEl) pulseEl.classList.add('bg-[#22AF85]');
            if (lastUpdateEl) lastUpdateEl.textContent = 'Updated just now';

        } catch (err) {
            console.error('Dashboard refresh failed:', err);
            if (pulseEl) pulseEl.classList.remove('bg-[#22AF85]');
            if (pulseEl) pulseEl.classList.add('bg-red-400');
            if (lastUpdateEl) lastUpdateEl.textContent = 'Update gagal';
        }
    }

    // ========== DOM Update Functions ==========

    function updateKpiCards(kpi) {
        // CS
        setText('#kpi-cs-leads', kpi.cs.leads);
        setText('#kpi-cs-closings', kpi.cs.closings);
        setText('#kpi-cs-conversion', kpi.cs.conversion);
        updateDelta('#kpi-cs-delta', kpi.cs.leads_delta);

        // Workshop
        setText('#kpi-ws-active', kpi.workshop.active);
        setText('#kpi-ws-completed', '✓ ' + kpi.workshop.completed);
        updateDelta('#kpi-ws-delta', kpi.workshop.completed_delta);
        setText('#kpi-ws-completed-count', kpi.workshop.completed);
        updateOverdueBadge('#kpi-ws-overdue', kpi.workshop.overdue);

        // Gudang
        const invValue = (kpi.gudang.inventory_value / 1000000).toFixed(1).replace('.', ',');
        setText('#kpi-gd-value', 'Rp ' + invValue);
        setText('#kpi-gd-stored', '📍 ' + kpi.gudang.stored_items);
        updateOverdueBadge('#kpi-gd-lowstock', kpi.gudang.low_stock, '🔻 ');

        // CX
        setText('#kpi-cx-rate', kpi.cx.resolution_rate);
        setText('#kpi-cx-avgtime', 'Avg Response: ' + kpi.cx.avg_response + 'h');
        setText('#kpi-cx-open', kpi.cx.open_issues);
        updateDelta('#kpi-cx-delta', kpi.cx.rate_delta);
    }

    function updateJourney(journey) {
        journey.forEach((node, i) => {
            const countEl = document.getElementById('journey-count-' + i);
            if (countEl) {
                countEl.textContent = node.count;
                countEl.style.display = node.count > 0 ? 'flex' : 'none';
            }
            const zeroEl = document.getElementById('journey-zero-' + i);
            if (zeroEl) zeroEl.style.display = node.count > 0 ? 'none' : 'flex';
        });
        setText('#journey-total', collect(journey).reduce((a, b) => a + b.count, 0));
    }

    function collect(arr) { return { reduce: arr.reduce.bind(arr) }; }

    function updateFunnelChart(funnel) {
        funnelChart.updateSeries([{
            name: 'Jumlah',
            data: funnel.map(d => d.count),
        }]);
    }

    function updateTechnicians(technicians) {
        const container = document.getElementById('technician-list');
        if (!container) return;

        if (!technicians || technicians.length === 0) {
            container.innerHTML = `
                <div class="text-center py-10">
                    <div class="text-4xl mb-2 opacity-30">🏆</div>
                    <div class="text-gray-300 text-xs italic">Belum ada data teknisi</div>
                    <div class="text-[9px] text-gray-300 mt-1">untuk periode ini</div>
                </div>`;
            return;
        }

        const medals = ['🥇','🥈','🥉'];
        const bgColors = ['bg-[#FFC232]/10 border-[#FFC232]/20', 'bg-white border-gray-100', 'bg-white border-gray-100', 'bg-white border-gray-100', 'bg-white border-gray-100'];
        const medalBg = ['bg-[#FFC232] text-yellow-900', 'bg-gray-300 text-gray-700', 'bg-orange-300 text-orange-800', 'bg-gray-100 text-gray-500', 'bg-gray-100 text-gray-500'];

        container.innerHTML = technicians.map((tech, i) => `
            <div class="flex items-center gap-3 p-3 rounded-xl ${bgColors[i]} border hover:shadow-md transition-all">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-black flex-shrink-0 ${medalBg[i]}">
                    ${i < 3 ? medals[i] : '#'+(i+1)}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-xs text-gray-800 truncate">${tech.name}</div>
                    <div class="text-[9px] text-gray-400 font-medium">${tech.specialization}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-base font-black text-[#22AF85]">${tech.count}</div>
                    <div class="text-[8px] text-gray-400 font-bold uppercase">Jobs</div>
                </div>
            </div>
        `).join('');
    }

    function updateRevenueChart(revenue) {
        revenueChart.updateOptions({
            xaxis: { categories: revenue.labels },
        });
        revenueChart.updateSeries([{
            name: 'Pendapatan',
            data: revenue.data,
        }]);
        const totalEl = document.getElementById('revenue-total');
        if (totalEl) totalEl.textContent = 'Rp ' + (revenue.total / 1000).toLocaleString('id-ID') + 'rb';
    }

    function updateTopIssues(issues) {
        const container = document.getElementById('top-issues-list');
        if (!container) return;

        if (!issues || issues.length === 0) {
            container.innerHTML = `
                <div class="text-center py-6">
                    <div class="text-3xl mb-1 opacity-30">🎉</div>
                    <div class="text-gray-300 text-xs italic">Tidak ada masalah CX</div>
                </div>`;
            return;
        }

        const maxCount = Math.max(...issues.map(i => i.count));
        container.innerHTML = issues.map(issue => {
            const pct = maxCount > 0 ? (issue.count / maxCount) * 100 : 0;
            return `
                <div>
                    <div class="flex justify-between text-[10px] font-bold mb-1">
                        <span class="text-gray-600 truncate max-w-[140px]">${issue.category || 'Uncategorized'}</span>
                        <span class="text-[#22AF85] font-black">${issue.count}</span>
                    </div>
                    <div class="w-full bg-gray-200/50 rounded-full h-2">
                        <div class="bg-gradient-to-r from-[#22AF85] to-[#22AF85]/60 h-2 rounded-full transition-all" style="width: ${pct}%"></div>
                    </div>
                </div>`;
        }).join('');
    }

    function updateComplaints(complaints) {
        setText('#complaint-pending', complaints.pending);
        setText('#complaint-process', complaints.process);
        setText('#complaint-overdue', complaints.overdue);
    }

    function updateUrgentActions(urgent) {
        updateSpkList('#overdue-list', urgent.overdue_spks, 'overdue');
        updateSpkList('#stuck-list', urgent.stuck_spks, 'stuck');
        updateCxOverdue(urgent.cx_overdue);
        updateLowStock(urgent.low_stock);

        // Update tab counts
        setText('#overdue-count', urgent.overdue_spks.length);
        setText('#stuck-count', urgent.stuck_spks.length);
        setText('#cx-overdue-count', urgent.cx_overdue.length);
    }

    function updateSpkList(selector, spks, type) {
        const container = document.querySelector(selector);
        if (!container) return;

        if (!spks || spks.length === 0) {
            const label = type === 'overdue' ? 'overdue' : 'stuck';
            container.innerHTML = `<div class="text-center py-6 text-gray-300 text-xs italic">✅ Tidak ada SPK ${label}</div>`;
            return;
        }

        const color = type === 'overdue' ? 'red' : 'orange';
        const icon = type === 'overdue' ? '🔥' : '⏸️';
        const baseUrl = <?php echo json_encode(url('reception'), 15, 512) ?>;

        container.innerHTML = spks.map(spk => `
            <a href="${baseUrl}/${spk.id}" class="flex items-center justify-between p-3 rounded-xl bg-${color}-50/50 border border-${color}-100 hover:bg-${color}-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-${color}-100 flex items-center justify-center">
                        <span class="text-xs">${icon}</span>
                    </div>
                    <div>
                        <div class="text-xs font-black text-gray-800 group-hover:text-${color}-600">${spk.spk_number}</div>
                        <div class="text-[10px] text-gray-400">${spk.customer_name}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-bold text-${color}-500">
                        ${type === 'overdue' ? spk.estimation_diff : ('Update ' + spk.updated_diff)}
                    </div>
                    <div class="text-[9px] text-gray-400">${spk.status}</div>
                </div>
            </a>
        `).join('');
    }

    function updateCxOverdue(issues) {
        const container = document.getElementById('cx-overdue-list');
        if (!container) return;

        if (!issues || issues.length === 0) {
            container.innerHTML = `<div class="text-center py-4 text-gray-300 text-xs italic">✅ Semua isu CX terkendali</div>`;
            return;
        }

        container.innerHTML = issues.map(issue => `
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-orange-50/50 border border-orange-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center text-xs">💬</div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-700">${issue.spk_number}</div>
                        <div class="text-[9px] text-gray-400 truncate max-w-[150px]">${issue.category}</div>
                    </div>
                </div>
                <div class="text-[10px] font-bold text-orange-500">${issue.created_diff}</div>
            </div>
        `).join('');
    }

    function updateLowStock(materials) {
        const container = document.getElementById('low-stock-list');
        if (!container) return;

        if (!materials || materials.length === 0) {
            container.innerHTML = `<div class="text-center py-4 text-gray-300 text-xs italic">✅ Semua stok aman</div>`;
            return;
        }

        container.innerHTML = materials.map(m => `
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-red-50/30 border border-red-100">
                <div>
                    <div class="text-[10px] font-bold text-gray-700">${m.name}</div>
                    <div class="text-[9px] text-gray-400">Min: ${m.min_stock} ${m.unit}</div>
                </div>
                <div class="px-2 py-0.5 bg-red-100 text-red-600 rounded-md text-[10px] font-black">
                    Sisa: ${m.stock}
                </div>
            </div>
        `).join('');
    }

    function updateQuickStats(stats) {
        updateBadge('#badge-lowstock', stats.low_stock_count);
        updateBadge('#badge-complaints', stats.pending_complaints);
    }

    // ========== Helpers ==========
    function setText(selector, value) {
        const el = document.querySelector(selector);
        if (el) el.textContent = value;
    }

    function updateDelta(selector, value) {
        const el = document.querySelector(selector);
        if (!el) return;
        if (value === 0) { el.style.display = 'none'; return; }
        el.style.display = 'inline-block';
        
        // If it's on a KPI card, use white/20 style, otherwise use light green/red
        if (el.classList.contains('font-bold') || el.closest('.stat-card')) {
            el.className = `px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/20 text-white`;
        } else {
            el.className = `px-2 py-0.5 rounded-md text-[10px] font-bold ${value > 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500'}`;
        }
        
        el.textContent = (value > 0 ? '↑' : '↓') + ' ' + Math.abs(value) + '%';
    }

    function updateOverdueBadge(selector, count, prefix = '') {
        const el = document.querySelector(selector);
        if (!el) return;
        
        if (count > 0) {
            el.style.display = 'flex';
            const textEl = el.querySelector('.count-text');
            if (textEl) {
                textEl.textContent = prefix + count;
            } else {
                // If it's the simplified badge without .count-text
                el.textContent = prefix + count;
            }
        } else {
            el.style.display = 'none';
        }
    }

    function updateBadge(selector, count) {
        const el = document.querySelector(selector);
        if (!el) return;
        if (count > 0) {
            el.style.display = 'flex';
            el.textContent = count;
        } else {
            el.style.display = 'none';
        }
    }

    // ========== Start Polling ==========
    setInterval(fetchDashboardData, POLL_INTERVAL);

    // Update "last updated" timer
    setInterval(() => {
        if (lastUpdateEl) {
            const secs = Math.floor((Date.now() - lastUpdate) / 1000);
            if (secs < 10) lastUpdateEl.textContent = 'Updated just now';
            else if (secs < 60) lastUpdateEl.textContent = secs + 's ago';
            else lastUpdateEl.textContent = Math.floor(secs / 60) + 'm ago';
        }
    }, 5000);

    // Live clock update
    setInterval(() => {
        const clockEl = document.getElementById('server-clock');
        if (clockEl) {
            clockEl.textContent = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        }
    }, 1000);

    console.log('🟢 Dashboard real-time polling active (every ' + (POLL_INTERVAL / 1000) + 's)');
});
</script>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/dashboard-v2/sections/charts-script.blade.php ENDPATH**/ ?>
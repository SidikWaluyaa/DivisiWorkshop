@props(['id', 'labels', 'datasets', 'height' => 80])

<canvas id="{{ $id }}" height="{{ $height }}"></canvas>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $id }}').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: @json($datasets)
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.4
                    },
                    point: {
                        radius: 3,
                        hoverRadius: 5
                    }
                }
            }
        });
    });
</script>

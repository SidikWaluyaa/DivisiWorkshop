@props(['id', 'labels', 'data', 'label' => 'Data', 'color' => '#0d9488', 'height' => 300])

<canvas id="{{ $id }}" height="{{ $height }}"></canvas>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $id }}').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: '{{ $label }}',
                    data: @json($data),
                    backgroundColor: '{{ $color }}',
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bars
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>

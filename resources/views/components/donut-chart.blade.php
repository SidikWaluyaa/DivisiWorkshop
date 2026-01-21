@props(['id', 'labels', 'data', 'colors', 'height' => 120])

<canvas id="{{ $id }}" height="{{ $height }}"></canvas>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $id }}').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($labels),
                datasets: [{
                    data: @json($data),
                    backgroundColor: @json($colors),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif"
                            }
                        }
                    }
                }
            }
        });
    });
</script>

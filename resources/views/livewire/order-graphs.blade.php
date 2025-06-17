<div class="bg-white p-4 shadow rounded">
<div class="p-4">
    <div class="mb-4">
       <div class="mb-4 space-x-2">
    <button wire:click="generateChartData('day')" class="btn btn-sm btn-outline-primary">Daily</button>
    <button wire:click="generateChartData('week')" class="btn btn-sm btn-outline-primary">Weekly</button>
    <button wire:click="generateChartData('month')" class="btn btn-sm btn-outline-primary">Monthly</button>
    <button wire:click="generateChartData('year')" class="btn btn-sm btn-outline-primary">Yearly</button>
</div>

    </div>

    <canvas id="ordersChart" width="600" height="300"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', () => {
            let chartInstance = null;

            function renderChart(chartData) {
                const ctx = document.getElementById('ordersChart').getContext('2d');

                if (chartInstance) chartInstance.destroy();

                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Orders',
                            data: chartData.data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Initial render
            renderChart(@js($chartData));

            // Update on Livewire event
            Livewire.hook('message.processed', (message, component) => {
                @this.chartData && renderChart(@js($chartData));
            });
        });
    </script>
</div>
 
</div>


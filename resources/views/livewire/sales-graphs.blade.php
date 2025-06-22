<div class="bg-white p-4 shadow rounded">
<div class="p-4">
    <div class="mb-4">
       <div class="mb-4 space-x-2">
  <button wire:click="generateChartData('day')" class="btn btn-sm btn-outline-primary">Daily</button>
<button wire:click="generateChartData('week')" class="btn btn-sm btn-outline-primary">Weekly</button>
<button wire:click="generateChartData('month')" class="btn btn-sm btn-outline-primary">Monthly</button>
<button wire:click="generateChartData('year')" class="btn btn-sm btn-outline-primary">Yearly</button>

</div>

<div class="mb-4 space-x-2">
    <h3 class="text-lg font-semibold">Sales Chart</h3>
</div>

    </div>

    <canvas id="ordersChart" width="600" height="300"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
    document.addEventListener('livewire:load', () => {
        let chartInstance = null;

        function renderChart(chartData) {
            const ctx = document.getElementById('ordersChart').getContext('2d');

            // Destroy old chart if it exists
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Create new chart
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets // ✅ correctly render multiple lines
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                callback: function (value) {
                                    return Number.isInteger(value) ? value : null;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initial chart load from backend
        renderChart(@js($chartData));

        // Re-render chart when Livewire dispatches an update
        window.addEventListener('update-chart', event => {
            renderChart(event.detail);
        });
    });
</script>
</div>
 
</div>


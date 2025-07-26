<div class="bg-white p-4 shadow rounded">
    <div class="p-4">
        <!-- Buttons to change grouping -->
        <div class="mb-4 space-x-2">
            <button wire:click="generateChartData('day')" class="btn btn-sm btn-outline-primary">Daily</button>
            <button wire:click="generateChartData('week')" class="btn btn-sm btn-outline-primary">Weekly</button>
            <button wire:click="generateChartData('month')" class="btn btn-sm btn-outline-primary">Monthly</button>
            <button wire:click="generateChartData('year')" class="btn btn-sm btn-outline-primary">Yearly</button>
        </div>

        <!-- Chart Title -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold">Sales Chart</h3>
        </div>

        <!-- Sales Chart Canvas -->
        <canvas id="salesChart" width="600" height="300"></canvas>
    </div>
</div>

<!-- Chart.js Script (only include once in your app layout ideally) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');

        window.salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
            }
        },
        scales: {
            y: {
                beginAtZero: false,      // ❌ Don't start at 0
                min: 1,                  // ✅ Start at 1
                ticks: {
                    stepSize: 1         // ✅ Increase by 1 each tick
                }
            }
        }
    }
});
        // Livewire event listener to update the chart
        window.addEventListener('update-chart', event => {
            const chartData = event.detail;

            // Update chart data
            salesChart.data.labels = chartData.labels;
            salesChart.data.datasets = chartData.datasets;

            // Refresh chart
            salesChart.update();
        });
    });
</script>

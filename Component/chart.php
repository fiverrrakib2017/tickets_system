<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Ticket Status Overview</h4>
                <canvas id="ticketStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Ticket Priority</h4>
                <canvas id="ticketPriorityChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* Ticket Status Chart */
const statusCtx = document.getElementById('ticketStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Open', 'Pending', 'Resolved', 'Closed'],
        datasets: [{
            data: [32, 18, 70, 10],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

/* Ticket Priority Chart */
const priorityCtx = document.getElementById('ticketPriorityChart').getContext('2d');
new Chart(priorityCtx, {
    type: 'bar',
    data: {
        labels: ['High', 'Medium', 'Low'],
        datasets: [{
            data: [15, 40, 25],
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

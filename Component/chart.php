<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Ticket Status Overview</h4>
                <canvas id="ticketStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Ticket Priority</h4>
                <canvas id="ticketPriorityChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
<?php
$priorityCounts = [
    1 => 0, // Low
    2 => 0, // Normal
    3 => 0, // Standard
    4 => 0, // Medium
    5 => 0, // High
    6 => 0, // Very High
];

$sql = "SELECT priority, COUNT(*) AS total 
        FROM ticket 
        GROUP BY priority";

$result = $con->query($sql);
while($row = $result->fetch_assoc()) {
    $priority = (int)$row['priority'];
    $count = (int)$row['total'];
    if(array_key_exists($priority, $priorityCounts)){
        $priorityCounts[$priority] = $count;
    }
}

$priorityData = json_encode(array_values($priorityCounts)); 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* Ticket Status Chart */
const statusCtx = document.getElementById('ticketStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Open', 'Pending', 'Resolved'],
        datasets: [{
            data: [32, 18, 70, 10],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});

/* Ticket Priority Chart */
const priorityCtx = document.getElementById('ticketPriorityChart').getContext('2d');
const priorityData = <?= $priorityData; ?>;
new Chart(priorityCtx, {
    type: 'bar',
    data: {
       labels: [
            'Low',
            'Normal',
            'Standard',
            'Medium',
            'High',
            'Very High'
        ],
        datasets: [{
            label: 'Tickets',
            data: priorityData,
            borderRadius: 6,
            backgroundColor: [
                '#0d6efd', // Low
                '#6f42c1', // Normal
                '#20c997', // Standard
                '#ffc107', // Medium
                '#dc3545', // High
                '#198754'  // Very High
            ]
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

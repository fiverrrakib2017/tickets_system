
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Offline Customer</h4>
                <table id="dashboard_customers_table" class="table table-bordered dt-responsive nowrap"
     style="border-collapse: collapse; border-spacing: 0; width: 100%;">
     <thead>
         <tr>
             <th>ID</th>
             <th>Name</th>
             <th>Phone</th>
             <th>POP/Area</th>
         </tr>
     </thead>
     <tbody id="">
         <?php
            /*------GET Service id And Find Customer id--------*/
            $customer_ids = [];
            include 'include/functions.php';
            
            function _formate_duration($seconds) {
                $h = floor($seconds / 3600);
                $m = floor(($seconds % 3600) / 60);
                return "{$h}h {$m}m";
            }
            /*-------------Request Total OFFLINE ip Customer---------------*/
                $offline_ip_query = $con->query(
                    "SELECT   `id`   FROM customers  WHERE ping_ip_status = 'offline'"
                );
                while ($row = $offline_ip_query->fetch_assoc()) {
                    $customer_ids[] = (int)$row['id'];
                }
            /*---- Remove duplicate IDs ----*/
            $customer_ids = array_unique($customer_ids);

            /*---- WHERE clause ----*/
            $where_clause = [];
            if (!empty($customer_ids)) {
                $ids_string = implode(',', $customer_ids);
                $where_clause[] = "c.id IN ($ids_string)";
            }

            /*---- Final WHERE ----*/
            $where_sql = '';
            if (!empty($where_clause)) {
                $where_sql = 'WHERE ' . implode(' AND ', $where_clause);
            }

            /*---- Main Query ----*/
            $sql = "SELECT 
                        c.*, 
                        COALESCE(ct.name, 'N/A') AS type_name,
                        COALESCE(pb.name, 'N/A') AS pop_branch_name,
                        GROUP_CONCAT(DISTINCT cp.phone_number SEPARATOR '<br>') AS phones,
                        GROUP_CONCAT(DISTINCT ci.ip_address SEPARATOR '<br>') AS public_ip_address
                    FROM customers c
                    LEFT JOIN customer_type ct 
                        ON c.customer_type_id = ct.id
                    LEFT JOIN pop_branch pb 
                        ON c.pop_id = pb.id
                    LEFT JOIN customer_phones cp
                        ON c.id = cp.customer_id
                    LEFT JOIN customer_public_ip_address ci
                        ON c.id = ci.customer_id
                    $where_sql
                    GROUP BY c.id
                    ORDER BY c.id DESC LIMIT 5
                    ";


            $result = mysqli_query($con, $sql);

            while ($rows = mysqli_fetch_assoc($result)) {
            ?>
         <tr>
             <td><?php echo $rows['id']; ?></td>
             <td>

                 <!-- Customer Name -->
                 <div class="fw-semibold mb-1">
                     <a href="customer_profile.php?clid=<?= (int) $rows['id'] ?>" class="text-decoration-none">
                         <?= htmlspecialchars($rows['customer_name']) ?>
                     </a>
                 </div>

                 <!-- Offline Duration -->
                 <?php if ($rows['ping_ip_status'] === 'offline' && !empty($rows['offline_duration'])): ?>
                 <div class="text-danger small mb-1">
                     <i class="fas fa-clock me-1"></i>
                     Offline for <?= _formate_duration((int) $rows['offline_duration']) ?>
                 </div>
                 <?php endif; ?>

                 <!-- Status + Ping -->
                 <div class="d-flex flex-column gap-1 small text-muted">

                     <!-- Online / Offline Badge -->
                     <div class="d-flex align-items-center gap-2">
                         <?php if ($rows['ping_ip_status'] === 'online'): ?>
                         <span class="badge bg-success">
                             <i class="fas fa-wifi me-1"></i> Online
                         </span>
                         <?php else: ?>
                         <span class="badge bg-danger">
                             <i class="fas fa-wifi-slash me-1"></i> Offline
                         </span>
                         <?php endif; ?>
                     </div>

                     <!-- Ping Statistics -->
                     <div class=" flex-wrap gap-3">

                         <span>
                             <i class="fas fa-paper-plane text-primary me-1"></i>
                             Sent: <strong><?= (int) $rows['ping_sent'] ?></strong>
                         </span><br>

                         <span>
                             <i class="fas fa-check-circle text-success me-1"></i>
                             Recv: <strong><?= (int) $rows['ping_received'] ?></strong>
                         </span><br>

                         <span>
                             <i class="fas fa-times-circle text-danger me-1"></i>
                             Lost: <strong><?= (int) $rows['ping_lost'] ?></strong>
                         </span><br>

                         <span>
                             <i class="fas fa-arrow-down text-info me-1"></i>
                             Min: <strong><?= (int) $rows['ping_min_ms'] ?> ms</strong>
                         </span><br>

                         <span>
                             <i class="fas fa-arrow-up text-warning me-1"></i>
                             Max: <strong><?= (int) $rows['ping_max_ms'] ?> ms</strong>
                         </span><br>

                         <span>
                             <i class="fas fa-chart-line text-secondary me-1"></i>
                             Avg: <strong><?= (int) $rows['ping_avg_ms'] ?> ms</strong>
                         </span><br>

                     </div>

                 </div>

             </td>
            
             <td>
                 <?php echo $rows['phones'] ? $rows['phones'] : 'N/A'; ?>
             </td>
             <td><?php echo htmlspecialchars($rows['pop_branch_name']); ?></td>
         </tr>
         <?php } ?>
     </tbody>

 </table>

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


$statusCounts = [
    'Open'     => 0,
    'Pending'  => 0,
    'Resolved' => 0,
];

$sql = "
    SELECT ticket_type, COUNT(*) AS total
    FROM ticket
    GROUP BY ticket_type
";

$result = $con->query($sql);

while($row = $result->fetch_assoc()){
    $type = $row['ticket_type'];
    $count = (int)$row['total'];

    switch($type){
        case 'Active':
        case 'Open':
            $statusCounts['Open'] += $count;
            break;
        case 'Pending':
            $statusCounts['Pending'] += $count;
            break;
        case 'Complete':
        case 'Close':
            $statusCounts['Resolved'] += $count;
            break;
    }
}

$statusData = json_encode(array_values($statusCounts)); 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

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

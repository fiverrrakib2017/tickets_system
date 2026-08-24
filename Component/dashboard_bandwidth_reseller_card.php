<?php
$bandwidth_customer_stats_sql = "SELECT 
    COUNT(*) as total_bandwidth_customer,
    SUM(CASE WHEN ping_ip_status = 'online' THEN 1 ELSE 0 END) as online_bandwidth_customer,
    SUM(CASE WHEN ping_ip_status = 'offline' THEN 1 ELSE 0 END) as offline_bandwidth_customer
FROM customers WHERE service_customer_type ='1'";

$bandwidth_total_stats_result = mysqli_query($con, $bandwidth_customer_stats_sql);
$bandwidth_stats = mysqli_fetch_assoc($bandwidth_total_stats_result);

$total_bandwidth_customer = $bandwidth_stats['total_bandwidth_customer'] ?? 0;
$online_bandwidth_customer = $bandwidth_stats['online_bandwidth_customer'] ?? 0;
$offline_bandwidth_customer = $bandwidth_stats['offline_bandwidth_customer'] ?? 0;
?>

<div class="row">
    <!-- Total POP Branch -->
    <div class="col-xl-4 col-md-6 mb-3">
        <a href="pop_branch.php" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Total Bandwidth Customer</p>
                            <h3 class="stat-value text-success mb-0"><?php echo $total_bandwidth_customer; ?></h3>
                            <small class="text-muted">Total registered branches</small>
                        </div>
                        <div class="stat-icon bg-primary text-white rounded-circle p-3">
                            <i class="mdi mdi-server-network mdi-24px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Online Bandwidth Customer -->
    <div class="col-xl-4 col-md-6 mb-3">
        <a href="pop_branch.php?status=online" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Online Bandwidth Customer</p>
                            <h3 class="stat-value text-success mb-0"><?php echo $online_bandwidth_customer; ?></h3>
                            <small class="text-muted">Active Bandwidth connections</small>
                        </div>
                        <div class="stat-icon bg-success text-white rounded-circle p-3">
                            <i class="mdi mdi-wifi-check mdi-24px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Offline Bandwidth Customer -->
    <div class="col-xl-4 col-md-6 mb-3">
        <a href="pop_branch.php?status=offline" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Offline Bandwidth Customer</p>
                            <h3 class="stat-value text-danger mb-0"><?php echo $offline_bandwidth_customer; ?></h3>
                            <small class="text-muted">Disconnected Bandwidth IPs</small>
                        </div>
                        <div class="stat-icon bg-danger text-white rounded-circle p-3">
                            <i class="mdi mdi-wifi-off mdi-24px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
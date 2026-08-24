<?php
$bandwidth_customer_stats_sql = "SELECT 
    COUNT(ping_ip) as total_mac_customer,
    SUM(CASE WHEN ping_ip_status = 'online' THEN 1 ELSE 0 END) as online_mac_customer,
    SUM(CASE WHEN ping_ip_status = 'offline' THEN 1 ELSE 0 END) as offline_mac_customer
FROM customers WHERE service_customer_type ='2'";

$bandwidth_total_stats_result = mysqli_query($con, $bandwidth_customer_stats_sql);
$bandwidth_stats = mysqli_fetch_assoc($bandwidth_total_stats_result);

$total_mac_customer = $bandwidth_stats['total_mac_customer'] ?? 0;
$online_mac_customer = $bandwidth_stats['online_mac_customer'] ?? 0;
$offline_mac_customer = $bandwidth_stats['offline_mac_customer'] ?? 0;
?>

<div class="row">
    <!-- Total POP Branch -->
    <div class="col-xl-4 col-md-6 mb-3">
        <a href="mac_reseller_customer.php" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Total MAC Customer</p>
                            <h3 class="stat-value text-success mb-0"><?php echo $total_mac_customer; ?></h3>
                            <small class="text-muted">Total registered MAC Customer</small>
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
        <a href="mac_reseller_customer.php?online_ip=true" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Online MAC Customer</p>
                            <h3 class="stat-value text-success mb-0"><?php echo $online_mac_customer; ?></h3>
                            <small class="text-muted">Active MAC connections</small>
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
        <a href="mac_reseller_customer.php?offline_ip=true" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Offline MAC Customer</p>
                            <h3 class="stat-value text-danger mb-0"><?php echo $offline_mac_customer; ?></h3>
                            <small class="text-muted">Disconnected MAC IPs</small>
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
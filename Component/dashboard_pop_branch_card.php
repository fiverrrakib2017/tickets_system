<?php
$pop_stats_sql = "SELECT 
    COUNT(*) as total_pop,
    SUM(CASE WHEN ping_ip_status = 'online' THEN 1 ELSE 0 END) as online_pop,
    SUM(CASE WHEN ping_ip_status = 'offline' THEN 1 ELSE 0 END) as offline_pop
FROM pop_branch";

$pop_stats_result = mysqli_query($con, $pop_stats_sql);
$pop_stats = mysqli_fetch_assoc($pop_stats_result);

$total_pop = $pop_stats['total_pop'] ?? 0;
$online_pop = $pop_stats['online_pop'] ?? 0;
$offline_pop = $pop_stats['offline_pop'] ?? 0;
?>

<div class="row">
    <!-- Total POP Branch -->
    <div class="col-xl-4 col-md-6 mb-3">
        <a href="pop_branch.php" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Total POP Branch</p>
                            <h3 class="stat-value text-success mb-0"><?php echo $total_pop; ?></h3>
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

    <!-- Online POP -->
    <div class="col-xl-4 col-md-6 mb-3">
        <a href="pop_branch.php?status=online" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Online POP</p>
                            <h3 class="stat-value text-success mb-0"><?php echo $online_pop; ?></h3>
                            <small class="text-muted">Active branch connections</small>
                        </div>
                        <div class="stat-icon bg-success text-white rounded-circle p-3">
                            <i class="mdi mdi-wifi-check mdi-24px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Offline POP -->
    <div class="col-xl-4 col-md-6 mb-3">
        <a href="pop_branch.php?status=offline" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title text-muted mb-1">Offline POP</p>
                            <h3 class="stat-value text-danger mb-0"><?php echo $offline_pop; ?></h3>
                            <small class="text-muted">Disconnected branch IPs</small>
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
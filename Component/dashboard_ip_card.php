

<div class="row">
    <!-- Total Bandwidth -->
    <div class="col-xl-3 col-md-6">
        <a href="bandwidth_customer.php?status=total">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title">Total Bandwidth</p>
                            <h3 class="stat-value"><?php echo $total_bandwidth ?? 0; ?><i> MBPS</i></h3>
                            <small class="text-muted">Cumulative bandwidth usage</small>
                        </div>
                        <div class="stat-icon bg-primary">
                            <i class="mdi mdi-signal-cellular-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total IP -->
    <div class="col-xl-3 col-md-6">
        <a href="customers.php?total_ip=1">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title">Total IP Addresses</p>
                            <h3 class="stat-value"><?php echo $total_ip ?? 0; ?></h3>
                            <small class="text-muted">Registered IP addresses</small>
                        </div>
                        <div class="stat-icon bg-info">
                            <i class="mdi mdi-ip-network"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Online IP -->
    <div class="col-xl-3 col-md-6">
        <a href="customers.php?online_ip=true">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title">Online IP</p>
                            <h3 class="stat-value"><?php echo $up_ip ?? 0; ?></h3>
                            <small class="text-muted">Currently active connections</small>
                        </div>
                        <div class="stat-icon bg-success">
                            <i class="mdi mdi-lan-connect"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Offline IP -->
    <div class="col-xl-3 col-md-6">
        <a href="customers.php?offline_ip=true">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title">Offline IP</p>
                            <h3 class="stat-value"><?php echo $down_ip ?? 0; ?></h3>
                            <small class="text-muted">Inactive or disconnected IPs</small>
                        </div>
                        <div class="stat-icon bg-danger">
                            <i class="mdi mdi-lan-disconnect"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>


</div>

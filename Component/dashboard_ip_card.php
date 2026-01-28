<style>
    .stat-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .stat-title {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .stat-value {
        font-weight: 700;
        margin-bottom: 2px;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
    }
</style>


<div class="row">
    <!-- Total Bandwidth -->
    <div class="col-xl-3 col-md-6">
        <a href="tickets.php?status=total">
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
        <a href="tickets.php?status=open">
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
        <a href="tickets.php?status=pending">
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
        <a href="tickets.php?status=resolved">
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

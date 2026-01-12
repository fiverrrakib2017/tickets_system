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
    <!-- Total Tickets -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Total Tickets</p>
                        <h3 class="stat-value">120</h3>
                        <small class="text-muted">All time tickets</small>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Tickets -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Open Tickets</p>
                        <h3 class="stat-value">32</h3>
                        <small class="text-muted">Currently open</small>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-folder-open"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Tickets -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Pending Tickets</p>
                        <h3 class="stat-value">18</h3>
                        <small class="text-muted">Awaiting response</small>
                    </div>
                    <div class="stat-icon bg-danger">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolved Tickets -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Resolved Tickets</p>
                        <h3 class="stat-value">70</h3>
                        <small class="text-muted">Successfully closed</small>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

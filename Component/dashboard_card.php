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
<?php
$todayStats = [
    'total'    => 0,
    'open'     => 0,
    'pending'  => 0,
    'resolved' => 0,
];

$today_start = date('Y-m-d 00:00:00');
$tomorrow_start = date('Y-m-d 00:00:00', strtotime('+1 day'));
$today_date = date('Y-m-d');

$sql = "SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN ticket_type = 'Active' THEN 1 ELSE 0 END) AS open_ticket,
        SUM(CASE WHEN ticket_type = 'Pending' THEN 1 ELSE 0 END) AS pending_ticket,
        SUM(CASE WHEN ticket_type = 'Complete' AND COALESCE(enddate, create_date) >= '$today_start' AND COALESCE(enddate, create_date) < '$tomorrow_start' THEN 1 ELSE 0 END) AS resolved_ticket
    FROM ticket 
    WHERE (is_management_ticket = 0 OR is_management_ticket IS NULL)
      AND create_date >= '$today_start' AND create_date < '$tomorrow_start'
";

$result = $con->query($sql);

if ($row = $result->fetch_assoc()) {
    $todayStats['total']    = (int)$row['total'];
    $todayStats['open']     = (int)$row['open_ticket'];
    $todayStats['pending']  = (int)$row['pending_ticket'];
    $todayStats['resolved'] = (int)$row['resolved_ticket'];
}

$userTicketStats = [];

$sqlUserStats = "
    SELECT 
        tu.id,
        tu.fullname AS name,
        SUM(CASE WHEN t.ticket_type = 'Active' THEN 1 ELSE 0 END) AS open_ticket,
        SUM(CASE WHEN t.ticket_type = 'Pending' THEN 1 ELSE 0 END) AS pending_ticket,
        SUM(CASE WHEN t.ticket_type = 'Complete' THEN 1 ELSE 0 END) AS resolved_ticket
    FROM ticket t
    INNER JOIN users tu ON t.assign_user_id = tu.id
    WHERE t.create_date >= '$today_start' 
      AND t.create_date < '$tomorrow_start'
    GROUP BY tu.id, tu.fullname
";

$resultUserStats = $con->query($sqlUserStats);

while ($row = $resultUserStats->fetch_assoc()) {
    $userTicketStats[] = $row;
}

$total_tickets = (int) ($con->query("SELECT COUNT(*) AS total_tickets FROM ticket")
                        ->fetch_assoc()['total_tickets'] ?? 0);


$internal_tickets_row = $con->query("
    SELECT
        SUM(CASE 
            WHEN pop_id != 0 
            AND created_at >= '$today_start' 
            AND created_at < '$tomorrow_start' 
            THEN 1 ELSE 0 
        END) AS today_noc,

        SUM(CASE 
            WHEN pop_id != 0 
            THEN 1 ELSE 0 
        END) AS total_noc,

        SUM(CASE 
            WHEN upstream_id != 0 
            AND created_at >= '$today_start' 
            AND created_at < '$tomorrow_start' 
            THEN 1 ELSE 0 
        END) AS today_upstream,

        SUM(CASE 
            WHEN upstream_id != 0 
            THEN 1 ELSE 0 
        END) AS total_upstream

    FROM internal_tickets
");

$internal_tickets = $internal_tickets_row->fetch_assoc();

/*------ NOC Incident -------*/
$noc_incident_tickets = $con->query("
    SELECT 
        SUM(
            CASE 
                WHEN create_date >= '$today_start' AND create_date < '$tomorrow_start' THEN 1
                ELSE 0
            END
        ) AS today_noc_incident_ticket,

        COUNT(*) AS total_noc_incident_ticket

    FROM noc_incident
");

$noc_incident_ticket = $noc_incident_tickets->fetch_assoc();
?>
<div class="row">

    <!-- Total & Today Tickets -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Tickets</p>
                        <h3 class="stat-value">
                            <a href="tickets.php?status=total&date_from=<?= date('Y-m-d'); ?>&date_to=<?= date('Y-m-d'); ?>"><?= $todayStats['total']; ?></a>
                            /
                            <a href="tickets.php"><?= $total_tickets; ?></a>
                        </h3>
                        <small class="text-muted">Today / Total</small>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- NOC & Incident -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">NOC Incident</p>
                        <h3 class="stat-value">
                            <a href="noc_incident.php?filter=today">
                                <?=  $noc_incident_ticket['today_noc_incident_ticket']; ?>
                            </a>    /
                            <a href="noc_incident.php">
                                <?=  $noc_incident_ticket['total_noc_incident_ticket']; ?>
                            </a>
                        </h3>
                        <small class="text-muted">Today / Total</small>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-network-wired"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Backbone Main Link -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title"> Backbone Main Link</p>
                        <h3 class="stat-value">
                            <a href="internal_tickets.php?department=noc_backbone&filter=today">
                                <?= $internal_tickets['today_noc'] ?>
                            </a>
                            /
                            <a href="internal_tickets.php?department=noc_backbone">
                                <?= $internal_tickets['total_noc'] ?>
                            </a>
                        </h3>
                        <small class="text-muted">Today / Total</small>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-network-wired"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Upstream -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Upstream Tickets</p>
                        <h3 class="stat-value">
                            <a href="internal_tickets.php?department=upstream&filter=today">
                                <?= $internal_tickets['today_upstream'] ?>
                            </a>
                            /
                            <a href="internal_tickets.php?department=upstream">
                                <?= $internal_tickets['total_upstream'] ?>
                            </a>
                        </h3>
                        <small class="text-muted">Today / Total</small>
                    </div>
                    <div class="stat-icon bg-danger">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">

    <!-- Open Tickets -->
    <div class="col-xl-4 col-md-6">
        <a href="tickets.php?status=open">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title">Open Tickets</p>
                            <h3 class="stat-value">  <?= $todayStats['open']; ?></h3>
                            <small class="text-muted">Currently open</small>
                             <div class="mt-2">
                                <?php foreach($userTicketStats as $user): ?>
                                    <?php if($user['open_ticket'] > 0): ?>
                                        <div style="font-size:12px;">
                                            <?= htmlspecialchars($user['name'] ?? 'N/A'); ?>
                                            - <strong><?= $user['open_ticket']; ?></strong>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Pending Tickets -->
    <div class="col-xl-4 col-md-6">
        <a href="tickets.php?status=pending">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-title">Pending Tickets</p>
                            <h3 class="stat-value"> <?= $todayStats['pending']; ?></h3>
                            <small class="text-muted">Awaiting response</small>
                             <div class="mt-2">
                                <?php foreach($userTicketStats as $user): ?>
                                    <?php if($user['pending_ticket'] > 0): ?>
                                        <div style="font-size:12px;">
                                            <?= htmlspecialchars($user['name'] ?? 'N/A'); ?>
                                            - <strong><?= $user['pending_ticket']; ?></strong>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="stat-icon bg-danger">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Resolved Tickets -->
    <div class="col-xl-4 col-md-6">
        <a href="tickets.php?status=resolved&date_from=<?= date('Y-m-d'); ?>&date_to=<?= date('Y-m-d'); ?>">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Resolved Tickets</p>
                        <h3 class="stat-value"> <?= $todayStats['resolved']; ?></h3>
                        <small class="text-muted">Successfully closed</small>
                         <div class="mt-2">
                            <?php foreach($userTicketStats as $user): ?>
                                <?php if($user['resolved_ticket'] > 0): ?>
                                    <div style="font-size:12px;">
                                        <?= htmlspecialchars($user['name'] ?? 'N/A'); ?>
                                        - <strong><?= $user['resolved_ticket']; ?></strong>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        </a>
    </div>

</div>

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

$sql = "
    SELECT 
        COUNT(*) AS total,
        SUM(ticket_type = 'Active')   AS open_ticket,
        SUM(ticket_type = 'Pending')  AS pending_ticket,
        SUM(ticket_type = 'Complete') AS resolved_ticket
    FROM ticket
    WHERE DATE(create_date) = CURDATE()
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
        ta.id,
        ta.name,
        SUM(t.ticket_type = 'Active')   AS open_ticket,
        SUM(t.ticket_type = 'Pending')  AS pending_ticket,
        SUM(t.ticket_type = 'Complete') AS resolved_ticket
    FROM ticket t
    LEFT JOIN ticket_assign ta ON t.asignto = ta.id
    WHERE t.create_date >= CURDATE()
      AND t.create_date < CURDATE() + INTERVAL 1 DAY
    GROUP BY t.asignto
";

$resultUserStats = $con->query($sqlUserStats);

while ($row = $resultUserStats->fetch_assoc()) {
    $userTicketStats[] = $row;
}
$total_tickets = (int) ($con->query("SELECT COUNT(*) AS total_tickets FROM ticket")
                ->fetch_assoc()['total_tickets'] ?? 0);

$today_date= date('Y-m-d');

$internal_tickets_row = $con->query("
    SELECT
        SUM(CASE 
            WHEN pop_id != 0 
            AND DATE(created_at) = '$today_date' 
            THEN 1 ELSE 0 
        END) AS today_noc,

        SUM(CASE 
            WHEN pop_id != 0 
            THEN 1 ELSE 0 
        END) AS total_noc,

        SUM(CASE 
            WHEN pop_id = 0 
            AND DATE(created_at) = '$today_date' 
            THEN 1 ELSE 0 
        END) AS today_upstream,

        SUM(CASE 
            WHEN pop_id = 0 
            THEN 1 ELSE 0 
        END) AS total_upstream

    FROM internal_tickets
");

$internal_tickets = $internal_tickets_row->fetch_assoc();
?>

<div class="row">

    <!-- Total & Today Tickets -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">Tickets</p>
                        <h3 class="stat-value">
                            <a href="tickets.php?status=total"><?= $todayStats['total']; ?></a>
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

    <!-- NOC & Backbone -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="stat-title">NOC & Backbone</p>
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
    <div class="col-xl-4 col-md-6">
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
        <a href="tickets.php?status=resolved">
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

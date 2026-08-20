<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/security_token.php';
include 'include/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<?php

// Current Active Ticket
$current_ticket = $con->query("
SELECT COUNT(*) total
FROM ticket
WHERE ticket_type='Active'
")->fetch_assoc()['total'];

// Pending Ticket
$pending_ticket = $con->query("
SELECT COUNT(*) total
FROM ticket
WHERE ticket_type='Pending'
")->fetch_assoc()['total'];

// Today Closed Ticket
$today_closed = $con->query("
SELECT COUNT(*) total
FROM ticket
WHERE ticket_type='Complete'
AND DATE(create_date)=CURDATE()
")->fetch_assoc()['total'];

// Total Closed Ticket
$total_closed = $con->query("
SELECT COUNT(*) total
FROM ticket
WHERE ticket_type='Complete'
")->fetch_assoc()['total'];

?>
<!doctype html>
<html lang="en">

<?php require 'Head.php'; ?>
<style>
   


.stat-card {
    position: relative;
    overflow: hidden;
    background: #fff;
    padding: 22px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(226, 232, 240, 0.9);
    transition: all 0.25s ease;
    min-height: 115px;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.10);
}

.stat-card::after {
    content: "";
    position: absolute;
    right: -30px;
    top: -30px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    opacity: 0.08;
    background: currentColor;
}

.stat-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.08);
}

.stat-card-body {
    text-align: right;
    z-index: 1;
}

.stat-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
}

.stat-value {
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    color: #0f172a;
}

/* Color themes */
.stat-primary {
    color: #2563eb;
}
.stat-primary .stat-card-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
}

.stat-success {
    color: #16a34a;
}
.stat-success .stat-card-icon {
    background: linear-gradient(135deg, #22c55e, #15803d);
    color: #fff;
}

.stat-info {
    color: #0891b2;
}
.stat-info .stat-card-icon {
    background: linear-gradient(135deg, #06b6d4, #0e7490);
    color: #fff;
}

.stat-warning {
    color: #d97706;
}
.stat-warning .stat-card-icon {
    background: linear-gradient(135deg, #f59e0b, #b45309);
    color: #fff;
}



.custom-table thead {
    background: #f8fafc;
}

.custom-table th {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    padding: 12px 16px;
}

.custom-table td {
    padding: 14px 16px;
    vertical-align: middle;
}

.custom-table tbody tr:hover {
    background: #f9fafb;
}

/* Avatar */
.avatar-sm {
    width: 34px;
    height: 34px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

/* Status badge */
.status-badge {
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 20px;
    font-weight: 500;
}

.status-badge.success {
    background: rgba(34,197,94,0.1);
    color: #16a34a;
}

.status-badge.info {
    background: rgba(6,182,212,0.1);
    color: #0891b2;
}


/* Avatar System */
.avatar-sm {
  width: 38px;
  height: 38px;
  position: relative;
}

.avatar-title {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
}

/* Progress Bar Overrides */
.progress {
  background-color: #edf2f7;
  overflow: hidden;
  border-radius: 10px;
}

/* Card Hover Effect */
.leaderboard-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.leaderboard-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 0.5rem 1.2rem rgba(0, 0, 0, 0.08) !important;
}

/* Rank Badges (Gold, Silver, Bronze) */
.rank-badge {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
}

.rank-1 {
  background-color: #ffd700;
  color: #5a4300;
  box-shadow: 0 2px 4px rgba(255, 215, 0, 0.4);
}

.rank-2 {
  background-color: #e0e0e0;
  color: #424242;
}

.rank-3 {
  background-color: #cd7f32;
  color: #ffffff;
}

/* Background Soft Highlights */
.bg-soft-warning-light {
  background-color: #fffdf5;
  border-radius: 8px;
}

/* Count Badge Pill */
.custom-count-badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
}

.bg-soft-primary { background-color: #e8f1ff; color: #0d6efd; }
.bg-soft-info    { background-color: #e0f8f9; color: #0dcaf0; }
.bg-soft-success { background-color: #e6f4ea; color: #198754; }
.bg-soft-warning { background-color: #fff8e6; color: #ffb800; }
.fs-7 { font-size: 0.75rem; }




.status-indicator {
    position: absolute;
    bottom: 2px;
    right: 0;
    width: 11px;
    height: 11px;
    border: 2px solid #fff;
    border-radius: 50%;
}

.bg-success {
    background-color: #28a745 !important;
}

.bg-secondary {
    background-color: #6c757d !important;
}
</style>
<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = ' ';
        include 'Header.php'; ?>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <?php include 'Sidebar_menu.php'; ?>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                  <div class="container-fluid py-4">

                       <div class="row mb-3">
                            <div class="col-12">
                                <h4 class="fw-muted">Dashboard Overview 👋</h4>
                                <p class="text-muted">Welcome back! Here’s what’s happening today.</p>
                            </div>
                        </div>

                       <div class="row g-3 mb-2">
                            <!-- Current Ticket -->
                            <div class="col-md-6 col-xl-3">
                                <div class="stat-card stat-primary">

                                    <div class="stat-card-icon">
                                        <i class="mdi mdi-ticket-confirmation-outline"></i>
                                    </div>

                                    <div class="stat-card-body">
                                        <span class="stat-label">Current Ticket</span>
                                        <h3 class="stat-value mb-0">
                                            <?= number_format($current_ticket) ?>
                                        </h3>
                                    </div>

                                </div>
                            </div>

                            <!-- Pending Task -->
                            <div class="col-md-6 col-xl-3">
                                <div class="stat-card stat-warning">

                                    <div class="stat-card-icon">
                                        <i class="mdi mdi-progress-clock"></i>
                                    </div>

                                    <div class="stat-card-body">
                                        <span class="stat-label">Pending Task</span>
                                        <h3 class="stat-value mb-0">
                                            <?= number_format($pending_ticket) ?>
                                        </h3>
                                    </div>

                                </div>
                            </div>

                            <!-- Today Closed -->
                            <div class="col-md-6 col-xl-3">
                                <div class="stat-card stat-success">

                                    <div class="stat-card-icon">
                                        <i class="mdi mdi-check-decagram-outline"></i>
                                    </div>

                                    <div class="stat-card-body">
                                        <span class="stat-label">Today Closed</span>
                                        <h3 class="stat-value mb-0">
                                            <?= number_format($today_closed) ?>
                                        </h3>
                                    </div>

                                </div>
                            </div>

                            <!-- Total Closed -->
                            <div class="col-md-6 col-xl-3">
                                <div class="stat-card stat-info">

                                    <div class="stat-card-icon">
                                        <i class="mdi mdi-clipboard-check-multiple-outline"></i>
                                    </div>

                                    <div class="stat-card-body">
                                        <span class="stat-label">Total Closed</span>
                                        <h3 class="stat-value mb-0">
                                            <?= number_format($total_closed) ?>
                                        </h3>
                                    </div>

                                </div>
                            </div>
                        </div>

                       <!--------- ENGINEER PERFORMANCE TABLE--------->
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm border-0 rounded-lg">
                                
                                <!-- Card Header -->
                                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-soft-primary text-primary rounded-circle me-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-users-cog fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold text-dark">Engineer Performance</h5>
                                        <small class="text-muted">Real-time task tracking & metrics</small>
                                    </div>
                                    </div>
                                    <button class="btn btn-sm btn-light text-muted shadow-none">
                                    <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>

                                <!-- Table Body -->
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 ">
                                        <thead>
                                        <tr>
                                            <th class="ps-4">Engineer</th>
                                            <th class="text-center">Current</th>
                                            <th class="text-center">Pending</th>
                                            <th class="text-center">Closed</th>
                                            <th class="text-center">Success Rate</th>
                                            <th class="pe-4" style="min-width: 180px;">Progress</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sql = "
                                                SELECT
                                                    tu.id,
                                                    tu.fullname as name,
                                                    tu.lastlogin,
                                                    SUM(CASE WHEN t.ticket_type='Active' THEN 1 ELSE 0 END) AS current_task,
                                                    SUM(CASE WHEN t.ticket_type='Pending' THEN 1 ELSE 0 END) AS pending_task,
                                                    SUM(CASE WHEN t.ticket_type='Complete' THEN 1 ELSE 0 END) AS closed_task
                                                FROM users tu
                                                LEFT JOIN ticket t ON t.assign_user_id = tu.id
                                                GROUP BY tu.id, tu.fullname
                                                ORDER BY closed_task DESC
                                            ";

                                            $engineers = $con->query($sql);
                                            ?>

                                            <?php while($row = $engineers->fetch_assoc()): 
                                                /*------Calculate Total Task--------*/
                                                $total_tasks = $row['current_task'] + $row['pending_task'] + $row['closed_task'];
                                                
                                                /*---------Dynamic Success Rate Division by zero---------*/ 
                                                $success_rate = ($total_tasks > 0) ? round(($row['closed_task'] / $total_tasks) * 100) : 0;
                                                
                                                /*---------Grace Color For performance----------*/ 
                                                $bar_color = 'bg-danger';
                                                if ($success_rate >= 80) {
                                                    $bar_color = 'bg-success';
                                                } elseif ($success_rate >= 50) {
                                                    $bar_color = 'bg-info';
                                                } elseif ($success_rate >= 30) {
                                                    $bar_color = 'bg-warning';
                                                }

                                                $first_letter = !empty($row['name']) ? strtoupper(substr($row['name'], 0, 1)) : 'U';
                                                $is_online = !empty($row['lastlogin']) && strtotime($row['lastlogin']) >= strtotime('-2 minutes');

                                                $status_class = $is_online ? 'bg-success' : 'bg-danger';
                                            ?>
                                                <tr style="border: 2px dotted #c7c7c7;">
                                                    <!-- Engineer Name & Avatar -->
                                                    <td class="ps-4">
                                                        <div class="d-flex align-items-center">

                                                            <div class="avatar-sm me-3 position-relative">

                                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-weight-bold">
                                                                    <?= $first_letter; ?>
                                                                </span>

                                                                <!-- Online / Offline indicator -->
                                                                <span 
                                                                    class="status-indicator <?= $status_class; ?>"
                                                                    title="<?= $is_online ? 'Online' : 'Offline'; ?>">
                                                                </span>

                                                            </div>

                                                            <div>
                                                                <h6 class="mb-0 font-weight-bold text-dark">
                                                                    <?= htmlspecialchars($row['name']); ?>
                                                                </h6>

                                                                <small class="text-muted">
                                                                    <?= $is_online ? 'Online' : 'Offline'; ?>
                                                                </small>
                                                            </div>

                                                        </div>
                                                    </td>

                                                    <!-- Current Task -->
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"><?= $row['current_task']; ?></span>
                                                    </td>

                                                    <!-- Pending Task -->
                                                    <td class="text-center">
                                                        <span class="badge bg-dark"><?= $row['pending_task']; ?></span>
                                                    </td>

                                                    <!-- Closed Task -->
                                                    <td class="text-center">
                                                        <span class="badge bg-success"><?= $row['closed_task']; ?></span>
                                                    </td>

                                                    <!-- Success Rate -->
                                                    <td class="text-center">
                                                        <span class="fw-bold text-dark"><?= $success_rate; ?>%</span>
                                                    </td>

                                                    <!-- Dynamic Progress Bar -->
                                                    <td class="pe-4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                <div class="progress-bar <?= $bar_color; ?> rounded" 
                                                                    role="progressbar" 
                                                                    style="width: <?= $success_rate; ?>%;" 
                                                                    aria-valuenow="<?= $success_rate; ?>" 
                                                                    aria-valuemin="0" 
                                                                    aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>

                                </div>
                            </div>
                        </div>


                       <!--------TOP PERFORMERS LEADERBOARD--------->
                       <?php
                        /*---------Today Top-----*/
                        $sql_today = "
                            SELECT tu.id, tu.fullname, COUNT(t.id) AS total_complete
                            FROM users tu
                            JOIN ticket t ON t.assign_user_id = tu.id
                            WHERE t.ticket_type = 'Complete' AND DATE(t.enddate) = CURDATE()
                            GROUP BY tu.id, tu.fullname
                            ORDER BY total_complete DESC
                            LIMIT 3
                        ";
                        $today_performers = $con->query($sql_today);

                        /*----------This Week Top-------*/
                        $sql_week = "
                            SELECT tu.id, tu.fullname, COUNT(t.id) AS total_complete
                            FROM users tu
                            JOIN ticket t ON t.assign_user_id = tu.id
                            WHERE t.ticket_type = 'Complete' 
                            AND YEARWEEK(t.enddate, 1) = YEARWEEK(CURDATE(), 1)
                            GROUP BY tu.id, tu.fullname
                            ORDER BY total_complete DESC
                            LIMIT 3
                        ";
                        $week_performers = $con->query($sql_week);

                        /*----------This Month Top---------*/
                        $sql_month = "
                            SELECT tu.id, tu.fullname, COUNT(t.id) AS total_complete
                            FROM users tu
                            JOIN ticket t ON t.assign_user_id = tu.id
                            WHERE t.ticket_type = 'Complete' 
                            AND MONTH(t.enddate) = MONTH(CURDATE()) 
                            AND YEAR(t.enddate) = YEAR(CURDATE())
                            GROUP BY tu.id, tu.fullname
                            ORDER BY total_complete DESC
                            LIMIT 3
                        ";
                        $month_performers = $con->query($sql_month);

                        /*-----------This Year Top-------*/ 
                        $sql_year = "
                            SELECT tu.id,tu.fullname, COUNT(t.id) AS total_complete
                            FROM users tu
                            JOIN ticket t ON t.assign_user_id = tu.id
                            WHERE t.ticket_type = 'Complete' 
                            AND YEAR(t.enddate) = YEAR(CURDATE())
                            GROUP BY tu.id, tu.fullname
                            ORDER BY total_complete DESC
                            LIMIT 3
                        ";
                        $year_performers = $con->query($sql_year);
                        ?>
                       <!---------- TOP PERFORMERS LEADERBOARD ------->
                        <div class="row">
                            
                            <!-- 1. Today Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow-sm border-0 rounded-lg leaderboard-card">
                                    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-dark fs-6">
                                            <i class="far fa-calendar-alt text-primary me-2"></i>Today Top
                                        </h6>
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-2 py-1 fs-7">Daily</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush border-0">
                                            <?php 
                                            $rank = 1;
                                            if($today_performers && $today_performers->num_rows > 0):
                                                while($row = $today_performers->fetch_assoc()): 
                                                    $is_top = ($rank == 1);
                                            ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-3 py-2 <?= $is_top ? 'bg-soft-warning-light' : ''; ?>">
                                                    <div class="d-flex align-items-center">
                                                        
                                                        <span class="<?= $is_top ? 'fw-bold text-dark' : 'fw-semibold text-secondary'; ?>">
                                                            <?= htmlspecialchars($row['fullname']); ?>
                                                        </span>
                                                    </div>
                                                    <a href="tickets.php?status=resolved&user_id=<?= $row['id'] ?>&performance=daily">
                                                        <span class="custom-count-badge <?= $is_top ? 'bg-warning text-dark' : 'bg-light text-dark'; ?> fw-bold">
                                                            <?= number_format($row['total_complete']); ?>
                                                        </span>
                                                    </a>
                                                </li>
                                            <?php 
                                                $rank++;
                                                endwhile; 
                                            else:
                                            ?>
                                                <li class="list-group-item text-center text-muted py-3">No data available</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. This Week Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow-sm border-0 rounded-lg leaderboard-card">
                                    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-dark fs-6">
                                            <i class="far fa-clock text-info me-2"></i>This Week Top
                                        </h6>
                                        <span class="badge bg-soft-info text-info rounded-pill px-2 py-1 fs-7">Weekly</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush border-0">
                                            <?php 
                                            $rank = 1;
                                            if($week_performers && $week_performers->num_rows > 0):
                                                while($row = $week_performers->fetch_assoc()): 
                                                    $is_top = ($rank == 1);
                                            ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-3 py-2 <?= $is_top ? 'bg-soft-warning-light' : ''; ?>">
                                                    <div class="d-flex align-items-center">
                                                        
                                                        <span class="<?= $is_top ? 'fw-bold text-dark' : 'fw-semibold text-secondary'; ?>">
                                                            <?= htmlspecialchars($row['fullname']); ?>
                                                        </span>
                                                    </div>

                                                    <a href="tickets.php?status=resolved&user_id=<?= $row['id'] ?>&performance=weekly">
                                                        <span class="custom-count-badge <?= $is_top ? 'bg-warning text-dark' : 'bg-light text-dark'; ?> fw-bold">
                                                            <?= number_format($row['total_complete']); ?>
                                                        </span>
                                                    </a>
                                                   
                                                </li>
                                            <?php 
                                                $rank++;
                                                endwhile; 
                                            else:
                                            ?>
                                                <li class="list-group-item text-center text-muted py-3">No data available</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. This Month Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow-sm border-0 rounded-lg leaderboard-card">
                                    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-dark fs-6">
                                            <i class="far fa-calendar text-success me-2"></i>This Month Top
                                        </h6>
                                        <span class="badge bg-soft-success text-success rounded-pill px-2 py-1 fs-7">Monthly</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush border-0">
                                            <?php 
                                            $rank = 1;
                                            if($month_performers && $month_performers->num_rows > 0):
                                                while($row = $month_performers->fetch_assoc()): 
                                                    $is_top = ($rank == 1);
                                            ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-3 py-2 <?= $is_top ? 'bg-soft-warning-light' : ''; ?>">
                                                    <div class="d-flex align-items-center">
                                                       
                                                        <span class="<?= $is_top ? 'fw-bold text-dark' : 'fw-semibold text-secondary'; ?>">
                                                            <?= htmlspecialchars($row['fullname']); ?>
                                                        </span>
                                                    </div>
                                                     <a href="tickets.php?status=resolved&user_id=<?= $row['id']; ?>&performance=monthly">
                                                        <span class="custom-count-badge <?= $is_top ? 'bg-warning text-dark' : 'bg-light text-dark'; ?> fw-bold">
                                                            <?= number_format($row['total_complete']); ?>
                                                        </span>
                                                    </a>
                                                   
                                                </li>
                                            <?php 
                                                $rank++;
                                                endwhile; 
                                            else:
                                            ?>
                                                <li class="list-group-item text-center text-muted py-3">No data available</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. This Year Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow-sm border-0 rounded-lg leaderboard-card">
                                    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-dark fs-6">
                                            <i class="fas fa-trophy text-warning me-2"></i>This Year Top
                                        </h6>
                                        <span class="badge bg-soft-warning text-warning rounded-pill px-2 py-1 fs-7">Yearly</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush border-0">
                                            <?php 
                                            $rank = 1;
                                            if($year_performers && $year_performers->num_rows > 0):
                                                while($row = $year_performers->fetch_assoc()): 
                                                    $is_top = ($rank == 1);
                                            ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-3 py-2 <?= $is_top ? 'bg-soft-warning-light' : ''; ?>">
                                                    <div class="d-flex align-items-center">
                                                        
                                                        <span class="<?= $is_top ? 'fw-bold text-dark' : 'fw-semibold text-secondary'; ?>">
                                                            <?= htmlspecialchars($row['fullname']); ?>
                                                        </span>
                                                    </div>
                                                    <a href="tickets.php?status=resolved&user_id=<?= $row['id'] ?>&performance=yearly">
                                                        <span class="custom-count-badge <?= $is_top ? 'bg-warning text-dark' : 'bg-light text-dark'; ?> fw-bold">
                                                            <?= number_format($row['total_complete']); ?>
                                                        </span>
                                                    </a>
                                                   
                                                </li>
                                            <?php 
                                                $rank++;
                                                endwhile; 
                                            else:
                                            ?>
                                                <li class="list-group-item text-center text-muted py-3">No data available</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php include 'Footer.php'; ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>


    <?php include 'script.php'; ?>
    <script type="text/javascript"></script>
    <script type="text/javascript">
        $('select').select2({
            width: '100%'
        });

       
    </script>
</body>

</html>

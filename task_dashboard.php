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




.custom-card {
    border: none;
   
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    overflow: hidden;
}

.custom-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 18px 20px;
    border-bottom: 1px solid #f1f5f9;
}

.custom-header h5 {
    margin: 0;
    font-size: 16px;
}

.view-btn {
    font-size: 13px;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.view-btn:hover {
    text-decoration: underline;
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





/* Table Styles */
.custom-performance-table thead th {
  background-color: #f8f9fa;
  color: #6c757d;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  border-bottom: 1px solid #edf2f7;
  padding-top: 12px;
  padding-bottom: 12px;
}

.custom-performance-table tbody tr {
  transition: all 0.2s ease;
}

.custom-performance-table tbody tr:hover {
  background-color: #fcfdfe;
}

/* Custom Soft Badges */
.custom-badge {
  padding: 6px 12px;
  border-radius: 30px;
  font-size: 0.825rem;
  font-weight: 600;
  display: inline-block;
  min-width: 36px;
}

.bg-soft-primary { background-color: #e8f1ff; color: #0d6efd; }
.bg-soft-success { background-color: #e6f4ea; color: #198754; }
.bg-soft-warning { background-color: #fff8e6; color: #ffb800; }
.bg-soft-danger  { background-color: #fce8e8; color: #dc3545; }
.bg-soft-info    { background-color: #e0f8f9; color: #0dcaf0; }

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

.status-indicator {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 2px solid #ffffff;
}

/* Progress Bar Overrides */
.progress {
  background-color: #edf2f7;
  overflow: hidden;
  border-radius: 10px;
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

                       <!-- ================= ENGINEER PERFORMANCE TABLE ================= -->
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
          <table class="table table-hover align-middle mb-0 custom-performance-table">
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
              
              <!-- Engineer 1: Rakib -->
              <tr>
                <td class="ps-4">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3 position-relative">
                      <span class="avatar-title rounded-circle bg-soft-primary text-primary font-weight-bold">R</span>
                      <span class="status-indicator bg-success"></span>
                    </div>
                    <div>
                      <h6 class="mb-0 font-weight-bold text-dark">Rakib</h6>
                      <small class="text-muted">Senior Engineer</small>
                    </div>
                  </div>
                </td>
                <td class="text-center"><span class="custom-badge bg-soft-primary text-primary">12</span></td>
                <td class="text-center"><span class="custom-badge bg-soft-warning text-warning">2</span></td>
                <td class="text-center"><span class="custom-badge bg-soft-success text-success">10</span></td>
                <td class="text-center">
                  <span class="fw-bold text-dark">83%</span>
                </td>
                <td class="pe-4">
                  <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                      <div class="progress-bar bg-success rounded" role="progressbar" style="width: 83%;" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Engineer 2: Sakib -->
              <tr>
                <td class="ps-4">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3 position-relative">
                      <span class="avatar-title rounded-circle bg-soft-info text-info font-weight-bold">S</span>
                      <span class="status-indicator bg-success"></span>
                    </div>
                    <div>
                      <h6 class="mb-0 font-weight-bold text-dark">Sakib</h6>
                      <small class="text-muted">Network Engineer</small>
                    </div>
                  </div>
                </td>
                <td class="text-center"><span class="custom-badge bg-soft-primary text-primary">8</span></td>
                <td class="text-center"><span class="custom-badge bg-soft-warning text-warning">1</span></td>
                <td class="text-center"><span class="custom-badge bg-soft-success text-success">7</span></td>
                <td class="text-center">
                  <span class="fw-bold text-dark">88%</span>
                </td>
                <td class="pe-4">
                  <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                      <div class="progress-bar bg-info rounded" role="progressbar" style="width: 88%;" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Engineer 3: Rony -->
              <tr>
                <td class="ps-4">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3 position-relative">
                      <span class="avatar-title rounded-circle bg-soft-danger text-danger font-weight-bold">R</span>
                      <span class="status-indicator bg-secondary"></span>
                    </div>
                    <div>
                      <h6 class="mb-0 font-weight-bold text-dark">Rony</h6>
                      <small class="text-muted">Support Engineer</small>
                    </div>
                  </div>
                </td>
                <td class="text-center"><span class="custom-badge bg-soft-primary text-primary">5</span></td>
                <td class="text-center"><span class="custom-badge bg-soft-warning text-warning">3</span></td>
                <td class="text-center"><span class="custom-badge bg-soft-success text-success">2</span></td>
                <td class="text-center">
                  <span class="fw-bold text-dark">40%</span>
                </td>
                <td class="pe-4">
                  <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                      <div class="progress-bar bg-danger rounded" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
                        <!-- ================= TOP PERFORMERS LEADERBOARD ================= -->
                        <div class="row">
                            
                            <!-- Today Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-dark text-white font-weight-bold text-center">
                                Today Top Performer
                                </div>
                                <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥇 Rakib</span>
                                    <span class="badge bg-primary rounded-pill">18</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥈 Sakib</span>
                                    <span class="badge bg-secondary rounded-pill">14</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥉 Rony</span>
                                    <span class="badge bg-secondary rounded-pill">12</span>
                                </li>
                                </ul>
                            </div>
                            </div>

                            <!-- This Week Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-dark text-white font-weight-bold text-center">
                                This Week Top Performer
                                </div>
                                <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥇 Rakib</span>
                                    <span class="badge bg-primary rounded-pill">81</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥈 Sohel</span>
                                    <span class="badge bg-secondary rounded-pill">76</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥉 Sakib</span>
                                    <span class="badge bg-secondary rounded-pill">70</span>
                                </li>
                                </ul>
                            </div>
                            </div>

                            <!-- This Month Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-dark text-white font-weight-bold text-center">
                                This Month Top Performer
                                </div>
                                <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥇 Rakib</span>
                                    <span class="badge bg-primary rounded-pill">220</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥈 Sakib</span>
                                    <span class="badge bg-secondary rounded-pill">206</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥉 Sohel</span>
                                    <span class="badge bg-secondary rounded-pill">198</span>
                                </li>
                                </ul>
                            </div>
                            </div>

                            <!-- This Year Top Performer -->
                            <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-dark text-white font-weight-bold text-center">
                                This Year Top Performer
                                </div>
                                <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥇 Rakib</span>
                                    <span class="badge bg-primary rounded-pill">2480</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥈 Sohel</span>
                                    <span class="badge bg-secondary rounded-pill">2344</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🥉 Sakib</span>
                                    <span class="badge bg-secondary rounded-pill">2230</span>
                                </li>
                                </ul>
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

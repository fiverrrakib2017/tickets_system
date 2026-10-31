<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<?php
// ==================== MANAGEMENT TICKETS ====================
$mgmt_current_ticket = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Active' AND is_management_ticket = 1
")->fetch_assoc()['total'];

$mgmt_pending_ticket = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Pending' AND is_management_ticket = 1
")->fetch_assoc()['total'];

$mgmt_today_closed = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Complete' AND DATE(create_date)=CURDATE() AND is_management_ticket = 1
")->fetch_assoc()['total'];

$mgmt_total_closed = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Complete' AND is_management_ticket = 1
")->fetch_assoc()['total'];


// ==================== NOC TICKETS ====================
$noc_current_ticket = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Active' AND (is_management_ticket = 0 OR is_management_ticket IS NULL)
")->fetch_assoc()['total'];

$noc_pending_ticket = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Pending' AND (is_management_ticket = 0 OR is_management_ticket IS NULL)
")->fetch_assoc()['total'];

$noc_today_closed = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Complete' AND DATE(create_date)=CURDATE() AND (is_management_ticket = 0 OR is_management_ticket IS NULL)
")->fetch_assoc()['total'];

$noc_total_closed = $con->query("
SELECT COUNT(*) total FROM ticket WHERE ticket_type='Complete' AND (is_management_ticket = 0 OR is_management_ticket IS NULL)
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
    <div id="">


        <!-- ========== Left Sidebar Start ========== -->
       
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="">

            <div class="">
                <div class="container-fluid">
                  <div class="container-fluid py-4" id="all_content">

                        

                        <!-- Main Container for Fullscreen -->
                        <div id="ticketsDashboardContainer" style="background: inherit; padding: 10px; border-radius: 8px;">

                            <!-- ==================== MANAGEMENT SECTION ==================== -->
                            <h4 class="mb-3 text-secondary font-weight-bold">Management Tickets</h4>
                            <div class="row g-3 mb-4 d-flex">
                                <!-- Current Ticket -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=open&is_management=1">
                                        <div class="stat-card stat-primary">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-ticket-confirmation-outline"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Current Ticket</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($mgmt_current_ticket) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Pending Task -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=pending&is_management=1">
                                        <div class="stat-card stat-warning">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-progress-clock"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Pending Task</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($mgmt_pending_ticket) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Today Closed -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=resolved&ticket_date=today&is_management=1">
                                        <div class="stat-card stat-success">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-check-decagram"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Today Closed</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($mgmt_today_closed) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Total Closed -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=resolved&is_management=1"> 
                                        <div class="stat-card stat-info">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-clipboard-check-multiple-outline"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Total Closed</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($mgmt_total_closed) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>


                            <!-- ==================== NOC SECTION ==================== -->
                            <h4 class="mb-3 text-secondary font-weight-bold">NOC Tickets</h4>
                            <div class="row g-3 mb-2 d-flex">
                                <!-- Current Ticket -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=open&is_management=0">
                                        <div class="stat-card stat-primary">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-ticket-confirmation-outline"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Current Ticket</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($noc_current_ticket) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Pending Task -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=pending&is_management=0">
                                        <div class="stat-card stat-warning">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-progress-clock"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Pending Task</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($noc_pending_ticket) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Today Closed -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=resolved&ticket_date=today&is_management=0">
                                        <div class="stat-card stat-success">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-check-decagram"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Today Closed</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($noc_today_closed) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Total Closed -->
                                <div class="col-md-6 col-xl-3">
                                    <a href="tickets.php?status=resolved&is_management=0"> 
                                        <div class="stat-card stat-info">
                                            <div class="stat-card-icon">
                                                <i class="mdi mdi-clipboard-check-multiple-outline"></i>
                                            </div>
                                            <div class="stat-card-body">
                                                <span class="stat-label">Total Closed</span>
                                                <h3 class="stat-value mb-0">
                                                    <?= number_format($noc_total_closed) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                        </div>

                        <!-- JavaScript for Fullscreen Mode -->
                        <script>
                        // Fullscreen Function
                        function toggleFullscreen(elementId) {
                            const elem = document.getElementById(elementId);
                            if (!document.fullscreenElement) {
                                if (elem.requestFullscreen) {
                                    elem.requestFullscreen();
                                } else if (elem.webkitRequestFullscreen) { /* Safari */
                                    elem.webkitRequestFullscreen();
                                } else if (elem.msRequestFullscreen) { /* IE11 */
                                    elem.msRequestFullscreen();
                                }
                            } else {
                                if (document.exitFullscreen) {
                                    document.exitFullscreen();
                                } else if (document.webkitExitFullscreen) {
                                    document.webkitExitFullscreen();
                                } else if (document.msExitFullscreen) {
                                    document.msExitFullscreen();
                                }
                            }
                        }

                        // Auto Refresh every 1 minute (60000 milliseconds)
                        setTimeout(function() {
                            location.reload();
                        }, 60000);
                        </script>

                      
                    </div>
                </div> <!-- container-fluid -->
            </div>
           
        </div>
        <!-- end main content-->
    </div>
    <script type="text/javascript">
        $('select').select2({
            width: '100%'
        });
       

       
    </script>
</body>

</html>

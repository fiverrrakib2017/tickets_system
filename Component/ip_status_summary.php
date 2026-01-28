<?php 
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/ip_status_summary.php';
?>
<div class="d-flex align-items-center me-2 ip-status-wrap">
    <!-- TOTAL Bandwidth -->
    <div class="text-center px-3 border-end">
        <div class="fw-bold text-dark">
            <i class="mdi mdi-speedometer me-1"></i>
            <a href="customers.php"><?php echo $total_bandwidth ?? 0; ?> </a><i style="font-size: 10px;">MBPS</i>
        </div>
    </div>
    <!-- TOTAL IP -->
    <div class="text-center px-3 border-end">
        <div class="fw-bold text-dark">
            <i class="mdi mdi-ip-network me-1"></i>
            <a href="customers.php"><?php echo $total_ip ?? 0; ?></a>
        </div>
    </div>

    <!-- UP IP -->
    <div class="text-center px-3 border-end">
        <div class="fw-bold text-success">
            <i class="mdi mdi-arrow-up-bold-circle-outline me-1"></i>
            <a href="customers.php"><?php echo $up_ip ?? 0; ?></a>
        </div>
    </div>

    <!-- DOWN IP -->
    <div class="text-center px-3">
        <div class="fw-bold text-danger">
            <i class="mdi mdi-arrow-down-bold-circle-outline me-1"></i>
            <a href="customers.php"><?php echo $down_ip ?? 0; ?></a>
        </div>
    </div>
</div>

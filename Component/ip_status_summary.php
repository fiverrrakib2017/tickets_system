
<?php

$total_ip = $con->query("
    SELECT COUNT(DISTINCT ping_ip) AS total 
    FROM customers
")->fetch_assoc()['total'];

$up_ip = $con->query("
    SELECT COUNT(DISTINCT ping_ip) AS up_ip
    FROM customers
    WHERE ping_ip_status = 'online'
")->fetch_assoc()['up_ip'];

$down_ip = $con->query("
    SELECT COUNT(DISTINCT ping_ip) AS down_ip
    FROM customers
    WHERE ping_ip_status != 'online'
")->fetch_assoc()['down_ip'];

?>



<div class="d-flex align-items-center me-2 ip-status-wrap">
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

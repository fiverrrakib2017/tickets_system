<?php
date_default_timezone_set('Asia/Dhaka');
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
   $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if(isset($_SESSION['customer']['id']) ){
    $clid = $_SESSION['customer']['id'];

    $customer = [];

   $sql = "SELECT 
            c.*, 
            COALESCE(ct.name, 'N/A') AS type_name,
            COALESCE(pb.name, 'N/A') AS pop_branch_name,
            GROUP_CONCAT(DISTINCT cp.phone_number SEPARATOR '<br>') AS phones,
            GROUP_CONCAT(DISTINCT ci.ip_address SEPARATOR '<br>') AS public_ip_address

        FROM customers c
        LEFT JOIN customer_type ct 
            ON c.customer_type_id = ct.id
        LEFT JOIN pop_branch pb 
            ON c.pop_id = pb.id
        LEFT JOIN customer_phones cp
            ON c.id = cp.customer_id
        LEFT JOIN customer_public_ip_address ci
            ON c.id = ci.customer_id
        WHERE c.id = '$clid'
        ORDER BY c.id DESC 
        LIMIT 1";

    $customer_query = $con->query($sql);

    if($customer_query->num_rows > 0){
        $customer = $customer_query->fetch_assoc();
    }
} 

?>
<!doctype html>
<html lang="en">
<head>

    <meta charset="utf-8">
  
    <title>Tickets Management </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

  
    <!-- DataTables -->
<link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
<link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
<link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css">

<!-- Bootstrap Touchspin -->
<link href="../assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css">

<!-- Select2 -->
<link href="../assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css">

<!-- Bootstrap Datepicker -->
<link href="../assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

<!-- Spectrum Colorpicker -->
<link href="../assets/libs/spectrum-colorpicker2/spectrum.min.css" rel="stylesheet" type="text/css">

<!-- Bootstrap Css -->
<link href="../assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css">

<!-- Icons Css -->
<link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css">

<!-- App Css -->
<link href="../assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css">

<!-- Toastr Css -->
<link rel="stylesheet" type="text/css" href="../css/toastr/toastr.min.css">

<!-- Delete Modal Css -->
<link rel="stylesheet" type="text/css" href="../css/deleteModal.css">

<!-- Chartist Chart -->
<link href="../assets/libs/chartist/chartist.min.css" rel="stylesheet" type="text/css">

<!-- C3 Chart Css -->
<link href="../assets/libs/c3/c3.min.css" rel="stylesheet" type="text/css">

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <!-- Extra CSS per page -->
    <?php if (!empty($extra_css)) echo $extra_css; ?>
</head>
<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="">
       
       
        <!-- Left Sidebar End -->
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content" style="margin-left: 0px !important; ">
      
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="container">
                            <div class="main-body">
                            <a href="logout.php" class="btn btn-primary mb-2"> <i class="fas fa-sign-out-alt"></i>Logout</a>
                                <div class="row gutters-sm">
                                    <div class="col-md-4 mb-3">
                                        <div class="card  p-3 mb-4 bg-white rounded text-center">
                                            <div class="card-body">
                                                <div class="d-flex flex-column align-items-center profile">
                                                    <!-- Profile Image -->

                                                    <img src="<?php $_SERVER['DOCUMENT_ROOT']?>/assets/images/<?php echo $customer['profile_pic'] ?? 'avatar.png'; ?>"
                                                        class="rounded-circle border border-3 border-primary shadow-sm"
                                                        width="120" height="120" id="profilePreview"/>
                                                            <!-- Upload Button -->
                                                        <form id="profileImageForm" enctype="multipart/form-data" class="mt-2">
                                                            <label for="profileImageUpload" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-upload"></i> Change Photo
                                                            </label>
                                                            <input type="file" name="profile_image" id="profileImageUpload" accept="image/*" hidden />
                                                        </form>

                                                    <!-- Profile Details -->
                                                    <div class="mt-3">
                                                        <h6 class="text-primary fw-bold"><?php echo $customer['customer_name'] ?? 'N/A'; ?>-<?php echo htmlspecialchars($customer['pop_branch_name'] ?? 'N/A'); ?></h6>

                                                        <p class="text-muted mb-1">
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-network-wired"></i> Connection Via
                                                                <?php echo $customer['type_name'] ?? 'N/A'; ?>
                                                            </span>
                                                        </p>
                                                        <p class="text-muted mb-1">
                                                            <span class="badge bg-secondary">#
                                                                <?php echo $customer['id']; ?></span>
                                                        </p>

                                                        <!-- User Since -->
                                                        <small class="text-muted">
                                                            <i class="far fa-calendar-alt"></i>
                                                            <?php
                                                            $createdate = new DateTime(
                                                                $customer['created_at']
                                                            );
                                                            echo $createdate->format(
                                                                "d M, Y"
                                                            );
                                                            ?>
                                                        </small>
                                                        <!-- Client Type -->
                                                        <p class="text-muted mb-1">
                                                            <span class="badge bg-success">
                                                                <?php if($customer['service_customer_type'] == 1) echo "Bandwidth"; elseif($customer['customer_type_id'] == 2) echo "Mac Reseller"; else echo "Customer"; ?>
                                                            </span>
                                                        </p>
                                                        
                                                        <!-- Connection Status -->
                                                        <div class="mt-4">

                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body py-3">
                                                                    <!-- IP Update Form -->
                                                                    
                                                                    <form action="#" class="row g-2 align-items-center">

                                                                    <div class="col-md-7 d-flex align-items-center gap-2">
                                                                        <i class="fas fa-globe text-muted"></i>
                                                                        <label class="small text-muted mb-0">IP Address</label>
                                                                    </div>

                                                                    <div class="col-md-8">
                                                                        <input type="text"
                                                                            id="ping_ip"
                                                                            class="form-control"
                                                                            placeholder="e.g. 192.168.1.1"
                                                                            value="<?= htmlspecialchars($customer['ping_ip'] ?? ''); ?>">
                                                                    </div>


                                                                    </form>
                                                                    <?php
                                                                    $status = $customer['ping_ip_status'] ?? 'unknown';
                                                                    $isOnline = ($status === 'online');

                                                                    $badgeClass = $isOnline ? 'bg-success' : 'bg-danger';
                                                                    $iconClass  = $isOnline ? 'mdi-wifi' : 'mdi-wifi-off';
                                                                    $statusText = strtoupper($status);
                                                                    ?>

                                                                    <div id="ping_result" class="mt-3">
                                                                    <div class="">
                                                                        <div class="card-body p-3">

                                                                            <!-- Header -->
                                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                                <div>
                                                                                    <h6 class="mb-0 fw-semibold text-secondary">
                                                                                        <i class="mdi mdi-lan-connect me-1 text-primary"></i>
                                                                                        Ping Status
                                                                                    </h6>
                                                                                    <small class="text-muted">Real-time connectivity check</small>
                                                                                </div>

                                                                                <span class="badge <?= $badgeClass; ?> px-3 py-2 d-flex align-items-center gap-1">
                                                                                    <i class="mdi <?= $iconClass; ?>"></i>
                                                                                    <?= $statusText; ?>
                                                                                </span>
                                                                            </div>

                                                                            <!-- Packet Info -->
                                                                            <div class="row text-center g-2 mb-3">
                                                                                <div class="col-4">
                                                                                    <div class="p-2 bg-light rounded">
                                                                                        <div class="text-muted small">Sent</div>
                                                                                        <div class="fw-bold">
                                                                                            <?= htmlspecialchars($customer['ping_sent'] ?? 'N/A'); ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-4">
                                                                                    <div class="p-2 bg-light rounded">
                                                                                        <div class="text-muted small">Received</div>
                                                                                        <div class="fw-bold text-success">
                                                                                            <?= htmlspecialchars($customer['ping_received'] ?? 'N/A'); ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-4">
                                                                                    <div class="p-2 bg-light rounded">
                                                                                        <div class="text-muted small">Lost</div>
                                                                                        <div class="fw-bold text-danger">
                                                                                            <?= htmlspecialchars($customer['ping_lost'] ?? 'N/A'); ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Latency -->
                                                                            <div class="row text-center g-2">
                                                                                <div class="col-4">
                                                                                    <div class="p-2 border rounded">
                                                                                        <div class="text-muted small">Min</div>
                                                                                        <div class="fw-semibold">
                                                                                            <?= htmlspecialchars($customer['ping_min_ms'] ?? 'N/A'); ?> ms
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-4">
                                                                                    <div class="p-2 border rounded">
                                                                                        <div class="text-muted small">Max</div>
                                                                                        <div class="fw-semibold">
                                                                                            <?= htmlspecialchars($customer['ping_max_ms'] ?? 'N/A'); ?> ms
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-4">
                                                                                    <div class="p-2 border rounded bg-soft-primary">
                                                                                        <div class="text-muted small">Avg</div>
                                                                                        <div class="fw-bold text-primary">
                                                                                            <?= htmlspecialchars($customer['ping_avg_ms'] ?? 'N/A'); ?> ms
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Offline Duration -->
                                                                            <?php if (!$isOnline && !empty($customer['offline_since'])): ?>
                                                                                <div class="alert alert-danger mt-3 py-2 small text-center mb-0">
                                                                                    <i class="mdi mdi-alert-circle-outline me-1"></i>
                                                                                    <strong>Offline Since:</strong>
                                                                                    <?= htmlspecialchars($customer['offline_since']); ?>
                                                                                    <br>
                                                                                    <strong>Duration:</strong>
                                                                                    <?= gmdate("H:i:s", (int)$customer['offline_duration']); ?>
                                                                                </div>
                                                                            <?php endif; ?>

                                                                        </div>
                                                                    </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card border-0 rounded-4 shadow-sm">
                                            <div class="card-body p-0">
                                                
                                                <!-- Customer Links -->
                                                <!-- Fullname -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-account me-2 text-primary fs-5"></i>
                                                            <span class="fw-bold">Fullname:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($customer['customer_name'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>

                                                <!-- Email -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-email-outline me-2 text-success fs-5"></i>
                                                            <span class="fw-bold">Email:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($customer['customer_email'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>

                                                <!-- Phone -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-phone me-2 text-info fs-5"></i>
                                                            <span class="fw-bold">Phone:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark">
                                                            <?php 

                                                            $get_all_customer_phone_number = $con->query("SELECT * FROM customer_phones WHERE customer_id = '".$customer['id']."'");

                                                            if($get_all_customer_phone_number && $get_all_customer_phone_number->num_rows > 0){
                                                                while($phone_row = $get_all_customer_phone_number->fetch_array()){
                                                                    $rawPhone = $phone_row['phone_number'];
                                                                    $cleanPhone = preg_replace('/[^0-9+]/', '', $rawPhone); 

                                                                    echo '<div class="mb-1 d-flex align-items-center">';
                                                                    /*-------Phone Number---------*/
                                                                    echo '<span class="me-2">'.$rawPhone.'</span>';
                                                                    /*------- Phone Call Icon---------*/
                                                                    echo '<a href="tel:'.$cleanPhone.'" class="me-2" title="Call '.$rawPhone.'">
                                                                            <i class="mdi mdi-phone fs-5 text-success"></i>
                                                                        </a>';
                                                                    echo '<a href="https://wa.me/'.$cleanPhone.'" target="_blank" class="me-2" title="WhatsApp '.$rawPhone.'">
                                                                            <i class="mdi mdi-whatsapp fs-5 text-success"></i>
                                                                        </a>';
                                                                    echo '</div>';
                                                                }
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>



                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- VLAN -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-network me-2 text-warning fs-5"></i>
                                                            <span class="fw-bold">VLAN:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($customer['customer_vlan'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>

                                                <!-- IP Address -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-server-network me-2 text-danger fs-5"></i>
                                                            <span class="fw-bold">Public IP Address:</span>
                                                        </p>
                                                        <a href="customers.php?public_ip=<?php echo htmlspecialchars($customer['customer_ip'] ?? 'N/A'); ?>" class="text-decoration-none">
                                                            <span class="fw-semibold text-dark">
                                                                <?php echo ($customer['public_ip_address'] ?? 'N/A'); ?>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!-- IP Address -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-server-network me-2 text-danger fs-5"></i>
                                                            <span class="fw-bold">Private IP Address:</span>
                                                        </p>
                                                        <a href="customers.php?private_ip=<?php echo htmlspecialchars($customer['private_customer_ip'] ?? 'N/A'); ?>" class="text-decoration-none">
                                                            <span class="fw-semibold text-dark">
                                                                <?php echo htmlspecialchars($customer['private_customer_ip'] ?? 'N/A'); ?>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- POP / Area -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-map-marker-outline me-2 text-primary fs-5"></i>
                                                            <span class="fw-bold">POP / Area:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($customer['pop_branch_name'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>

                                                <!-- Connection Type -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-network-outline me-2 text-success fs-5"></i>
                                                            <span class="fw-bold">Connection Via:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($customer['type_name'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>
                                                <!-- Service Type -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <a href="customers.php?service=true" class="text-decoration-none">
                                                        <!-- Services -->
                                                        <p class="mb-0 text-muted">
                                                                <i class="mdi mdi-network-outline me-2 text-success fs-5"></i>
                                                                <span class="fw-bold">Service:</span>
                                                            </p>
                                                        </a>
                                                   
                                                        <span class="fw-semibold text-dark">
                                                            
                                                        <?php 
                                                            if($customer['service_customer_type'] == 1){
                                                                /* Bandwidth Service */
                                                            if(function_exists('get_customer_services')){
                                                                    print_r(get_customer_services($customer['id'])) ;
                                                                }
                                                            }elseif($customer['customer_type_id'] == 2){
                                                                /* Mac Reseller Service */
                                                                if(function_exists('get_customer_mac_reseller_services')){
                                                                    print_r(get_customer_mac_reseller_services($customer['id'])) ;
                                                                }
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            
                                                        ?>
                                                        </span>
                                                          
                                                    </div>
                                                </div>

                                               <!-- Total Capacity -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-speedometer me-2 text-primary fs-5"></i>
                                                            <span class="fw-bold">Total Capacity:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark">
                                                            <?php echo htmlspecialchars($customer['total'] ?? '0'); ?> MBPS
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Service Type -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-access-point-network me-2 text-success fs-5"></i>
                                                            <span class="fw-bold">Service Type:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark">
                                                            
                                                            <?php 
                                                            if($customer['service_type']==='NTTN'){
                                                                echo '<span class="badge bg-info">NTTN</span>'; 
                                                            }else if($customer['service_type']==='Overhead'){
                                                                echo '<span class="badge bg-warning text-dark">Overhead</span>';
                                                            }else if($customer['service_type']==='Both'){
                                                                echo '<span class="badge bg-success">Both</span>';
                                                            }else{
                                                                echo '<span class="badge bg-danger">N/A</span>';
                                                            }
                                                            
                                                            
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Status -->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-shield-check-outline me-2 text-success fs-5"></i>
                                                            <span class="fw-bold">Status:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark">
                                                            <?php 
                                                                echo ($customer['status'] == 1) 
                                                                    ? '<span class="badge bg-success">Active</span>' 
                                                                    : '<span class="badge bg-danger">Inactive</span>'; 
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <!------Nid File ---------->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-shield-check-outline me-2 text-success fs-5"></i>
                                                            <span class="fw-bold">Nid File:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark">
                                                            <?php 
                                                                if(!empty($customer['nid_file'])){
                                                                    echo '<a href="assets/customer/'.$customer['nid_file'].'" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>';
                                                                } else {
                                                                    echo 'N/A';
                                                                }
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <!------Service Agreement File ---------->
                                                <div class="col-12 bg-white p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-3 px-3">
                                                        <p class="mb-0 text-muted">
                                                            <i class="mdi mdi-shield-check-outline me-2 text-success fs-5"></i>
                                                            <span class="fw-bold">Service Agreement File:</span>
                                                        </p>
                                                        <span class="fw-semibold text-dark">
                                                            <?php 
                                                                if(!empty($customer['service_agreement_file'])){
                                                                    echo '<a href="../assets/customer/'.$customer['service_agreement_file'].'" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>';
                                                                } else {
                                                                    echo 'N/A';
                                                                }
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                 
                                    <div class="col-md-8">
                                        <div class="container">
                                           <div class="row">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <!-- Nav tabs -->
                                                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                                            <ul class="nav nav-tabs nav-tabs-custom flex-nowrap overflow-auto w-100" role="tablist" style="gap: 5px;">
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" data-bs-toggle="tab" href="#tickets" role="tab">
                                                                        <i class="mdi mdi-ticket-outline me-1"></i> Tickets
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <!-- Tab panes -->
                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="tickets" role="tabpanel">
                                                                <div class="table-responsive">
                                                                   <table id="tickets_table" class="table table-bordered dt-responsive nowrap"
                                                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                                        <thead>
                                                                            <?php include '../Table/tickets_head.php';?>
                                                                        </thead>
                                                                    <tbody id="tickets-list">
                                                                    <?php     
                                                                        $tickets = get_tickets($con, [
                                                                            'customer_id' => $customer['id']
                                                                        ]);
                                                                        include '../Table/tickets.php';
                                                                    ?>

                                                                    </tbody>

                                                                    </table>
                                                                </div>
                                                            </div>
                                                           
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!----------------Value Added Service--------------------->
                                            <div class="row">
                                                <div class="card">
                                                    <div class="card-body">
                                                         <?php include '../Table/value_added_service.php'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---------------- Customer Mikrotik Graph--------------------->
                                            <div class="row">
                                                <div class="card">
                                                    <div class="card-header bg-white">
                                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                                                            <!-- Title -->
                                                            <h4 class="card-title mb-0">
                                                                MikroTik Bandwidth Usage
                                                            </h4>

                                                            <!-- Config Form -->
                                                        <?php include '../Component/mikrotik_config_form.php'; ?>

                                                        </div>
                                                    </div>

                                                    <div class="card-body">
                                                         <?php include '../Component/mikrotik_graph.php'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            <!-- Modal for Ticket -->
            <?php 
            // require "modal/tickets_modal.php"; 
            ?>
          
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <!-- JAVASCRIPT -->
<script src="../assets/libs/jquery/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/metismenu/metisMenu.min.js"></script>
<script src="../assets/libs/simplebar/simplebar.min.js"></script> 
<script src="../assets/libs/node-waves/waves.min.js"></script> 
<script src="../assets/libs/select2/js/select2.min.js"></script>
<script src="../assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>

<!-- Required datatable js -->
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

<!-- Buttons examples -->
<script src="../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/libs/jszip/jszip.min.js"></script>
<script src="../assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="../assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script> 

<!-- Responsive examples -->
<script src="../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- Toastr -->
<script src="../js/toastr/toastr.min.js"></script>
<script src="../js/toastr/toastr.init.js"></script>

<!-- Datatable init js -->
<script src="../assets/js/pages/datatables.init.js"></script>

<!-- App Js -->
<script src="../assets/js/app.js"></script>

<!-- Peity chart -->
<script src="../assets/libs/peity/jquery.peity.min.js"></script> 

<!-- C3 Chart -->
<script src="../assets/libs/d3/d3.min.js"></script>
<script src="../assets/libs/c3/c3.min.js"></script> 

<!-- jQuery Knob -->
 <script src="../assets/libs/jquery-knob/jquery.knob.min.js"></script>

<!-- Dashboard init -->
<script src="../assets/js/pages/dashboard.init.js"></script>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<!-- Fluid Meter -->
 <script src="../assets/js/js-fluid-meter.js"></script>
<!-- Form Advanced Init -->
 <!-- <script src="assets/js/pages/form-advanced.init.js"></script>  -->

<!-- Plugin Js for Charts -->
<script src="../assets/libs/chartist/chartist.min.js"></script>
<script src="../assets/libs/chartist-plugin-tooltips/chartist-plugin-tooltip.min.js"></script>

 <!-- form wizard -->
 <script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>

<!-- form wizard init -->
<script src="../assets/js/pages/form-wizard.init.js"></script>
<!-- Counter-Up -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
  
<!-- Include Tickets js File -->
<script type="text/javascript">
    $('#tickets_table').dataTable();
</script>
</body>

</html>

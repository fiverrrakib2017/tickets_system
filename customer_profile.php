<?php
date_default_timezone_set('Asia/Dhaka');
include "include/security_token.php";
include "include/db_connect.php";
include 'include/functions.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_GET['clid'])){
    $clid = $_GET['clid'];

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
    } else {
        /*----Redirect if not found------*/ 
        header("Location: customers.php");
        exit();
    }
} else {
    /*----Redirect if no clid------*/ 
    header("Location: customers.php");
    exit();
}

?>
<!doctype html>
<html lang="en">
<?php 
$extra_css  = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
require 'Head.php';

?>
<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php  $page_title = "Profile";include "Header.php";?>
        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <!--- Sidemenu -->
                <?php include "Sidebar_menu.php"; ?>
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
                    <div class="row">
                        <div class="container">
                            <div class="main-body">
                                <div class="row gutters-sm">
                                    <div class="col-md-4 mb-3">
                                        <div class="card  p-3 mb-4 bg-white rounded text-center">
                                            <div class="card-body">
                                                <div class="d-flex flex-column align-items-center profile">
                                                    <!-- Profile Image -->

                                                    <img src="assets/images/<?php echo $customer['profile_pic'] ?? 'avatar.png'; ?>"
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
                                                        <h4 class="text-primary fw-bold"><?php echo $customer['customer_name'] ?? 'N/A'; ?></h4>
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
                                                        <!-- Action Buttons -->
                                                        <div class="mt-3">
                                                            <a href="customer_profile_edit.php?clid=<?php echo $clid; ?>"
                                                            class="btn btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Edit Profile
                                                            </a>
                                                        </div>
                                                        <!-- Connection Status -->
                                                        <div class="mt-4">

                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body py-3">
                                                                    <!-- IP Update Form -->
                                                                     <?php include 'Component/customer_ip.php';?>

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
                                                <?php include 'Component/customer_links.php';?>
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
                                                                    echo '<a href="assets/customer/'.$customer['service_agreement_file'].'" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>';
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
                                                                            <?php include 'Table/tickets_head.php';?>
                                                                        </thead>
                                                                    <tbody id="tickets-list">
                                                                    <?php     
                                                                        $tickets = get_tickets($con, [
                                                                            'customer_id' => $customer['id']
                                                                        ]);
                                                                        include 'Table/tickets.php';
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
                                                         <?php include 'Table/value_added_service.php'; ?>
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
                                                        <?php include 'Component/mikrotik_config_form.php'; ?>

                                                        </div>
                                                    </div>

                                                    <div class="card-body">
                                                         <?php include 'Component/mikrotik_graph.php'; ?>
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
            <?php require "modal/tickets_modal.php"; ?>
          
            <?php include "Footer.php"; ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include "script.php"; ?>
  
    <!-- Include Tickets js File -->
    <script src="js/tickets.js"></script>
    <script type="text/javascript">
        $('#tickets_table').dataTable();


    </script>
</body>

</html>

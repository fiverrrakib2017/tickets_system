<?php
include 'include/security_token.php';
include 'include/db_connect.php';
include 'include/functions.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//   $sql = "SELECT 
//             c.*, 
//             COALESCE(ct.name, 'N/A') AS type_name,
//             COALESCE(pb.name, 'N/A') AS pop_branch_name,
//             GROUP_CONCAT(DISTINCT cp.phone_number SEPARATOR ',') AS phones
//         FROM customers c
//         LEFT JOIN customer_type ct 
//             ON c.customer_type_id = ct.id
//         LEFT JOIN pop_branch pb 
//             ON c.pop_id = pb.id
//         LEFT JOIN customer_phones cp
//             ON c.id = cp.customer_id
        
//         GROUP BY c.id
//         ORDER BY c.id DESC";

//         $result = $con->query($sql);
//         while($rows=$result->fetch_array()){
//             echo '<pre>';
//            echo nl2br(str_replace(', ', "\n", $rows['phones']));
//             echo '</pre>';
//         }
//              exit;    
function _formate_duration($seconds) {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    return "{$h}h {$m}m";
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

        <?php $page_title = 'Customers';
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
                     <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="d-flex align-items-end flex-wrap">
                                    <div class="mr-md-3 mr-xl-5">
                                        <div class="d-flex">
                                            <i class="mdi mdi-home text-muted hover-cursor"></i>
                                            <p class="text-primary mb-0 hover-cursor">&nbsp;/&nbsp;<a href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor"><a href="customers.php">Customers</a></p>
                                            
                                            <?php if(isset($_GET['service_id'])): ?>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp;
                                                
                                                <?php
                                                    $service_id = (int)$_GET['service_id'];
                                                    $service_query = $con->prepare("SELECT name FROM customer_service WHERE id = ?");
                                                    $service_query->bind_param("i", $service_id);
                                                    $service_query->execute();
                                                    $service_result = $service_query->get_result();
                                                    if ($service_row = $service_result->fetch_assoc()) {
                                                        echo htmlspecialchars($service_row['name']);
                                                    } else {
                                                        echo "Unknown Service";
                                                    }
                                                ?>
                                            </p>
                                            <?php endif; ?>
                                            <?php if(isset($_GET['ip'])): ?>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp;
                                                <?php echo htmlspecialchars($_GET['ip']); ?>
                                            </p>
                                            <?php endif; ?>
                                            <?php if(isset($_GET['pop_branch'])): ?>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp; POP/&nbsp;
                                                
                                                <?php
                                                    $pop_branch_id = (int)$_GET['pop_branch'];
                                                    $pop_branch_query = $con->prepare("SELECT name FROM pop_branch WHERE id = ?");
                                                    $pop_branch_query->bind_param("i", $pop_branch_id);
                                                    $pop_branch_query->execute();
                                                    $pop_branch_result = $pop_branch_query->get_result();
                                                    if ($pop_branch_row = $pop_branch_result->fetch_assoc()) {
                                                        echo htmlspecialchars($pop_branch_row['name']);
                                                    } else {
                                                        echo "Unknown POP/Area";
                                                    }
                                                ?>
                                            </p>
                                            <?php endif; ?>
                                            <?php if(isset($_GET['customer_type'])): ?>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp;
                                                <?php
                                                    $customer_type_id = (int)$_GET['customer_type'];
                                                    $customer_type_query = $con->prepare("SELECT name FROM customer_type WHERE id = ?");
                                                    $customer_type_query->bind_param("i", $customer_type_id);
                                                    $customer_type_query->execute();
                                                    $customer_type_result = $customer_type_query->get_result();
                                                    if ($customer_type_row = $customer_type_result->fetch_assoc()) {
                                                        echo htmlspecialchars($customer_type_row['name']);
                                                    } else {
                                                        echo "Unknown Customer Type";
                                                    }
                                                ?>
                                            </p>
                                            <?php endif; ?>
                                            <?php if(isset($_GET['total_ip'])): ?>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp; Total IP Addresses (<?= $total_ip ?? 0 ?>)
                                            </p>
                                            <?php endif; ?>
                                            <?php if(isset($_GET['online_ip'])): ?>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp; Online IP (<?= $up_ip ?? 0 ?>)
                                            </p>
                                            <?php endif; ?>
                                            <?php if(isset($_GET['offline_ip'])): ?>
                                            <p class="text-primary mb-0 hover-cursor">
                                                &nbsp;/&nbsp; Offline IP (<?= $down_ip ?? 0 ?>)
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <br>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                               <div class="card-header customer_card_header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background-color: white;">
                                <!-- Add Customer Button -->
                                <a href="create_customer.php" class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i> Add New Customer
                                </a>
                            </div>

                                <div class="card-body">
                                    <div class="table-responsive ">
                                        <?php include 'Table/customer.php'; ?>

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
    <script src="js/Ajax.js"></script>
</body>

</html>

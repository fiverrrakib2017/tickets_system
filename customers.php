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
                                        <table id="customers_table" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>POP/Area</th>
                                                    <th>Type</th>
                                                    <th>Public IP</th>
                                                    <th>Private IP</th>
                                                    <th>Bandwidth</th>
                                                    <th>Capacity</th> 
                                                    <th>Status</th> 
                                                    <th>Action</th> 
                                                </tr>
                                            </thead>
                                          <tbody id="customer-list">
                                            <?php
                                            /*------GET Service id And Find Customer id--------*/
                                            $customer_ids = [];

                                            /*---- Service filter ----*/
                                            if (isset($_GET['service_id'])) {
                                                $service_id = (int)$_GET['service_id'];
                                                $service_query = $con->query(
                                                    "SELECT DISTINCT customer_id 
                                                    FROM customer_invoice 
                                                    WHERE service_id = $service_id"
                                                );
                                                while ($row = $service_query->fetch_assoc()) {
                                                    $customer_ids[] = (int)$row['customer_id'];
                                                }
                                            }

                                            /*---- Customer type filter ----*/
                                            if (isset($_GET['customer_type'])) {
                                                $customer_type_id = (int)$_GET['customer_type'];
                                                $customer_query = $con->query(
                                                    "SELECT id 
                                                    FROM customers 
                                                    WHERE customer_type_id = $customer_type_id"
                                                );
                                                while ($row = $customer_query->fetch_assoc()) {
                                                    $customer_ids[] = (int)$row['id'];
                                                }
                                            }

                                            /*---- Remove duplicate IDs ----*/
                                            $customer_ids = array_unique($customer_ids);

                                            /*---- WHERE clause ----*/
                                            $where_clause = [];
                                            if (!empty($customer_ids)) {
                                                $ids_string = implode(',', $customer_ids);
                                                $where_clause[] = "c.id IN ($ids_string)";
                                            }

                                            if (isset($_GET['public_ip']) && $_GET['public_ip'] !== '') {
                                                $ip = mysqli_real_escape_string($con, $_GET['public_ip']);
                                                $where_clause[] = "c.customer_ip = '$ip'";
                                            }
                                            if (isset($_GET['private_ip']) && $_GET['private_ip'] !== '') {
                                                $ip = mysqli_real_escape_string($con, $_GET['private_ip']);
                                                $where_clause[] = "c.private_customer_ip = '$ip'";
                                            }

                                            if (isset($_GET['pop_branch'])) {
                                                $pop_branch_id = (int)$_GET['pop_branch'];
                                                $where_clause[] = "c.pop_id = $pop_branch_id";
                                            }

                                            /*---- Final WHERE ----*/
                                            $where_sql = '';
                                            if (!empty($where_clause)) {
                                                $where_sql = 'WHERE ' . implode(' AND ', $where_clause);
                                            }

                                            /*---- Main Query ----*/
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
                                                    $where_sql
                                                    GROUP BY c.id
                                                    ORDER BY c.id DESC
                                                    ";


                                            $result = mysqli_query($con, $sql);

                                            while ($rows = mysqli_fetch_assoc($result)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $rows['id']; ?></td>
                                                <td>

                                                    <!-- Customer Name -->
                                                    <div class="fw-semibold mb-1">
                                                        <a href="customer_profile.php?clid=<?= (int)$rows['id']; ?>" class="text-decoration-none">
                                                            <?= htmlspecialchars($rows['customer_name']); ?>
                                                        </a>
                                                    </div>

                                                    <!-- Offline Duration -->
                                                    <?php if ($rows['ping_ip_status'] === 'offline' && !empty($rows['offline_duration'])): ?>
                                                        <div class="text-danger small mb-1">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Offline for <?= _formate_duration((int)$rows['offline_duration']); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Status + Ping -->
                                                    <div class="d-flex flex-column gap-1 small text-muted">

                                                        <!-- Online / Offline Badge -->
                                                        <div class="d-flex align-items-center gap-2">
                                                            <?php if ($rows['ping_ip_status'] === 'online'): ?>
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-wifi me-1"></i> Online
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-wifi-slash me-1"></i> Offline
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Ping Statistics -->
                                                        <div class="d-flex flex-wrap gap-3">

                                                            <span>
                                                                <i class="fas fa-paper-plane text-primary me-1"></i>
                                                                Sent: <strong><?= (int)$rows['ping_sent']; ?></strong>
                                                            </span>

                                                            <span>
                                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                                Recv: <strong><?= (int)$rows['ping_received']; ?></strong>
                                                            </span>

                                                            <span>
                                                                <i class="fas fa-times-circle text-danger me-1"></i>
                                                                Lost: <strong><?= (int)$rows['ping_lost']; ?></strong>
                                                            </span>

                                                            <span>
                                                                <i class="fas fa-arrow-down text-info me-1"></i>
                                                                Min: <strong><?= (int)$rows['ping_min_ms']; ?> ms</strong>
                                                            </span>

                                                            <span>
                                                                <i class="fas fa-arrow-up text-warning me-1"></i>
                                                                Max: <strong><?= (int)$rows['ping_max_ms']; ?> ms</strong>
                                                            </span>

                                                            <span>
                                                                <i class="fas fa-chart-line text-secondary me-1"></i>
                                                                Avg: <strong><?= (int)$rows['ping_avg_ms']; ?> ms</strong>
                                                            </span>

                                                        </div>

                                                    </div>

                                                </td>

                                                <td><?php echo htmlspecialchars($rows["customer_email"]); ?></td>
                                                <td>
                                                    <?php echo $rows['phones'] ? $rows['phones'] : 'N/A'; ?>
                                                </td>


                                                <td><?php echo htmlspecialchars($rows["pop_branch_name"]); ?></td>
                                                <td><?php echo htmlspecialchars($rows["type_name"]); ?></td>
                                                <td><?php echo ($rows["public_ip_address"]); ?></td>
                                                <td><?php echo ($rows["private_customer_ip"]); ?></td>
                                                <td>
                                                    <?php
                                                    if (function_exists('get_customer_services')) {
                                                        echo get_customer_services($rows['id']);
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo (int)$rows["total"]; ?> MBPS</td>
                                                <td>
                                                    <?php echo ($rows["status"] == 1)
                                                        ? '<span class="badge bg-success">Active</span>'
                                                        : '<span class="badge bg-danger">Inactive</span>'; ?>
                                                </td>
                                                <td style="text-align:right">
                                                    <a href="customer_profile_edit.php?clid=<?php echo $rows['id']; ?>" class="btn-sm btn btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" name="delete_button"
                                                        data-id="<?php echo $rows['id']; ?>"
                                                        class="btn-sm btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <a href="customer_profile.php?clid=<?php echo $rows['id']; ?>" class="btn-sm btn btn-success">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>

                                        </table>

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
    <!-- Delete Modal -->
    <div id="deleteModal" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="fa fa-trash"></i>
                    </div>
                    <h4 class="modal-title w-100">Are you sure?</h4>
                    <h4 class="modal-title w-100 d-none" id="DeleteId"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="True">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="DeleteConfirm">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Send Message -->
     <?php include 'modal/message_modal.php'; ?>
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <script src="js/Ajax.js"></script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
           
            $('#customers_table').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                    "targets": [2],
                    "orderable": false,
                }],
            });
            /** Delete Script **/
            $(document).on('click', "button[name='delete_button']", function() {
                var id = $(this).data('id');
                $('#DeleteId').text(id);
                $('#deleteModal').modal('show');

                $('#DeleteConfirm').off('click').on('click', function() {
                    $.ajax({
                        url: "include/customer_server.php?delete_customer_data=true",
                        type: "POST",
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#deleteModal').modal('hide');
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error("Error deleting NAS: " + xhr.responseText);
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>

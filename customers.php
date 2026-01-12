<?php
include 'include/security_token.php';
include 'include/db_connect.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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
                                            <p class="text-primary mb-0 hover-cursor">Customers</p>
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
                                <button data-bs-toggle="modal" data-bs-target="#addCustomerModal" type="button" class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i> Add New Customer
                                </button>
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
                                                    <th>IP</th>
                                                    <th>Bandwidth</th>
                                                    <th>Status</th> 
                                                    <th>Action</th> 
                                                </tr>
                                            </thead>
                                            <tbody id="customer-list">
                                                <?php
                                                $sql = "SELECT 
                                                            c.*, 
                                                            COALESCE(ct.name, 'N/A') AS type_name,
                                                            COALESCE(pb.name, 'N/A') AS pop_branch_name
                                                        FROM customers c
                                                        LEFT JOIN customer_type ct 
                                                            ON c.customer_type_id = ct.id
                                                        LEFT JOIN pop_branch pb 
                                                            ON c.pop_id = pb.id
                                                        ORDER BY c.id DESC";
                                                $result = mysqli_query($con, $sql);

                                                while ($rows = mysqli_fetch_assoc($result)) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $rows['id']; ?></td>
                                                    <td>
                                                        <a href="customer_profile.php?clid=<?php echo $rows['id']; ?>">
                                                            <?php echo htmlspecialchars($rows["customer_name"]); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($rows["customer_email"]); ?></td>
                                                    <td><?php echo htmlspecialchars($rows["customer_phone"]); ?></td>
                                                    <td><?php echo htmlspecialchars($rows["pop_branch_name"]); ?></td>
                                                    <td><?php echo htmlspecialchars($rows["type_name"]); ?></td>
                                                    <td><?php echo htmlspecialchars($rows["customer_ip"]); ?></td>
                                                    <td><?php echo htmlspecialchars($rows["customer_bandwidth"]); ?></td>
                                                    <td>
                                                        <?php echo ($rows["status"] == 1) ? '<span class="badge bg-success">Active</span>' 
                                                                                            : '<span class="badge bg-danger">Inactive</span>'; ?>
                                                    </td>
                                                    <td style="text-align:right">
                                                        <button type="button" name="edit_button" data-id="<?php echo $rows['id']; ?>" class="btn-sm btn btn-info">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" name="delete_button" data-id="<?php echo $rows['id']; ?>" class="btn-sm btn btn-danger">
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
   
    <!------------------  Customer Modal ------------------>
    <?php require 'modal/customer_modal.php'; ?>
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
        });
    </script>
</body>

</html>

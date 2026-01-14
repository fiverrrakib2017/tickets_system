<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/security_token.php';
include 'include/db_connect.php';
include 'include/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {

    $ticket_id = (int)$_GET['id'];

    $stmt = $con->prepare("SELECT * FROM ticket WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $ticket_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die('Ticket not found.');
    }

    $ticket = $result->fetch_assoc(); 

    $stmt->close();

} else {
    die('Invalid request.');
}


?>
<!doctype html>
<html lang="en">

<?php

require 'Head.php';

?>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Ticket Profile';
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
                        <div class="col-md-6 justify-content-end">
                            <div class="d-flex  gap-2 mb-3">

                                <a class="btn btn-sm btn-success" href="ticket_edit.php?id=<?php echo $ticket['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="ticket_delete.php?id=<?php echo $ticket['id']; ?>" 
                                class="btn btn-sm btn-primary"
                                onclick="return confirm('Are You Sure');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            
                                                           

                                    <button onclick="history.back();" type="button" class="btn btn-sm btn-danger">
                                        <i class="mdi mdi-arrow-left"></i> Back
                                    </button>
                                
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row gutters-sm">
                            <div class="col-md-4 mb-3">
                                <div class="card rounded-3 border-0">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="mdi mdi-ticket-confirmation"></i> Ticket Profile
                                        </h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="mdi mdi-account-circle text-primary"></i>
                                                    Customer Name:</p>
                                                <a href="#" class="text-dark fw-bold">
                                                    <?php 
                                                      $customer_id=$ticket['customer_id'];
                                                        $customer_name = '';

                                                        if ($customer_id > 0) {
                                                            $stmt = $con->prepare(
                                                                "SELECT customer_name FROM customers WHERE id = ? LIMIT 1"
                                                            );
                                                            $stmt->bind_param('i', $customer_id);
                                                            $stmt->execute();
                                                            $result = $stmt->get_result();

                                                            if ($row = $result->fetch_assoc()) {
                                                               echo  $customer_name = $row['customer_name'];
                                                            }

                                                            $stmt->close();
                                                        }

                                                      ?>
                                                </a>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="mdi mdi-marker-check text-success"></i>
                                                    Ticket Type:</p>
                                                <a href="#" class="text-dark fw-bold">
                                                    <?= htmlspecialchars($ticket['ticket_type']); ?>
                                                </a>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="mdi mdi-tag text-warning"></i> Ticket ID:
                                                </p>
                                                <a href="#" class="text-dark fw-bold">
                                                    <?= htmlspecialchars($ticket['id']); ?>
                                                </a>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="fas fa-user-cog text-danger"></i> Assigned
                                                    To:</p>
                                                <a href="#" class="text-dark fw-bold">
                                                   <?php 
                                                   $assign_to_id = $ticket['asignto'];
                                                   $assign_to_name = '';
                                                    if ($assign_to_id > 0) {
                                                         $stmt = $con->prepare(
                                                              "SELECT name FROM ticket_assign WHERE id = ? LIMIT 1"
                                                         );
                                                         $stmt->bind_param('i', $assign_to_id);
                                                         $stmt->execute();
                                                         $result = $stmt->get_result();
    
                                                         if ($row = $result->fetch_assoc()) {
                                                             echo  $assign_to_name = $row['name'];
                                                         }
    
                                                         $stmt->close();
                                                    }
                                                   
                                                   
                                                   ?>
                                                </a>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="ion ion-md-paper text-info"></i> Ticket For:
                                                </p>
                                                <a href="#" class="text-dark fw-bold">
                                                    <?= htmlspecialchars($ticket['ticketfor']); ?>
                                                </a>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="fas fa-envelope text-secondary"></i>
                                                    Complain Type:</p>
                                                <a href="#" class="text-dark fw-bold">
                                                    <?php 
                                                    $complain_type_id = $ticket['complain_type'];
                                                    $complain_type_name = '';
                                                     if ($complain_type_id > 0) {
                                                          $stmt = $con->prepare(
                                                               "SELECT topic_name FROM ticket_topic WHERE id = ? LIMIT 1"
                                                          );
                                                          $stmt->bind_param('i', $complain_type_id);
                                                          $stmt->execute();
                                                          $result = $stmt->get_result();
     
                                                          if ($row = $result->fetch_assoc()) {
                                                              echo  $complain_type_name = $row['topic_name'];
                                                          }
     
                                                          $stmt->close();
                                                     }
                                                     ?>
                                                 </a>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="fas fa-calendar-alt text-primary"></i> From Date:</p>
                                                <a href="#" class="text-dark fw-bold">
                                                    <?= htmlspecialchars(date('d M Y', strtotime($ticket['startdate']))); ?>
                                                </a>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0"><i class="fas fa-calendar-check text-success"></i> To
                                                    Date:</p>
                                                <a href="#" class="text-dark fw-bold">
                                                   <?php 
                                                   if($ticket['enddate'] == '0000-00-00' || empty($ticket['enddate'])) {
                                                       echo 'N/A';
                                                   } else {
                                                       echo htmlspecialchars(date('d M Y', strtotime($ticket['enddate'])));
                                                   }
                                                   
                                                   
                                                   ?>
                                                </a>
                                            </div>
                                           <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0">
                                                    <i class="mdi mdi-account-edit-outline text-info me-2"></i>
                                                    Customer Note:
                                                </p>
                                                <span class="text-dark fw-bold">
                                                    <?= htmlspecialchars($ticket['customer_note']); ?>
                                                </span>
                                            </div>

                                            <div class="list-group-item d-flex justify-content-between">
                                                <p class="mb-0">
                                                    <i class="mdi mdi-headset text-primary me-2"></i>
                                                    NOC Note:
                                                </p>
                                                <span class="text-dark fw-bold">
                                                    <?= htmlspecialchars($ticket['noc_note']); ?>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-8 ">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Comment</th>
                                                        <th>Progress</th>
                                                        <th>date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $stmt = $con->prepare("SELECT * FROM ticket_details WHERE tcktid = ? ORDER BY id DESC");
                                                    $stmt->bind_param('i', $ticket['id']);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    while ($comment = $result->fetch_assoc()) {
                                                        ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($comment['id']); ?></td>
                                                            <td><?= nl2br(htmlspecialchars($comment['comments'])); ?></td>
                                                            <td><?= htmlspecialchars($comment['parcent']); ?></td>
                                                            <td><?= htmlspecialchars(date('d M Y H:i', strtotime($comment['datetm']))); ?></td>
                                                        </tr>
                                                        <?php
                                                    }

                                                    $stmt->close();
                                                    ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-body">
                                        <form action="include/tickets_server.php?add_ticket_comment=true" method="POST" id="commentForm">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <div class="form-group mb-2">
                                                        <label>Ticket Status</label>
                                                        <input type="text" class="d-none" name="ticket_id"
                                                            value="<?= $ticket['id']; ?>">
                                                        <select name="ticket_type" class="form-select">
                                                            <option value="">Select</option>
                                                            <option value="Active" <?= $ticket['ticket_type'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                                            <option value="Pending" <?= $ticket['ticket_type'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="Complete" <?= $ticket['ticket_type'] == 'Complete' ? 'selected' : '' ?>>Complete</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-group mb-2">
                                                        <label>Progress</label>
                                                        <select name="progress" class="form-select">
                                                            <option value="">Select</option>
                                                            <option value="0%">0%</option>
                                                            <option value="15%">15%</option>
                                                            <option value="25%">25%</option>
                                                            <option value="35%">35%</option>
                                                            <option value="45%">45%</option>
                                                            <option value="55%">55%</option>
                                                            <option value="65%">65%</option>
                                                            <option value="75%">75%</option>
                                                            <option value="85%">85%</option>
                                                            <option value="95%">95%</option>
                                                            <option value="100%">100%</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-group mb-2">
                                                        <label for="">Assigned To</label>
                                                        <select name="assign_to" class="form-select" required>
                                                            <option value="">---select---</option>
                                                            <?php
                                                                $ticket_assign = $con->query('SELECT * FROM ticket_assign');
                                                                while ($row = $ticket_assign->fetch_assoc()) {
                                                                    $selected = $row['id'] == $ticket['asignto'] ? 'selected' : '';
                                                                    echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-sm">
                                                    <div class="form-group mb-2">
                                                        <label>Write Comment</label>
                                                        <textarea class="form-control"  rows="3" name="comment"
                                                            placeholder="Enter Your Comment"></textarea>
                                                    </div>
                                                </div>

                                                <!--  div conditionally show -->
                                                <div id="completionBox" class="mb-2 d-none col-sm">
                                                    <div class="form-group mb-2">
                                                        <label for="customerMessage">Customer Message</label>
                                                        <textarea class="form-control" id="customerMessage" rows="3" placeholder=""></textarea>
                                                        
                                                        <!-- CheckBox -->
                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="checkbox" id="sendToCustomer">
                                                            <label class="form-check-label" for="sendToCustomer">
                                                                Send message to customer
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Changes</button>
                                        </form>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title">Latest Tickets</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tickets_table" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No.</th> 
                                                    <th>Status</th> 
                                                    <th>Created</th>
                                                    <th>Priority</th>
                                                    <th>Customer Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Issues</th>
                                                    <th>Pop/Area</th>                                                   
                                                    <th>Assigned</th>
                                                    <th>Acctual Work</th>
                                                    <th>Completed</th>
                                                    <th>Percentage</th>
                                                    <th>Customer Note</th>
                                                    <th>NOC Note</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                          <tbody id="tickets-list">
                                                <?php
$sql = "
SELECT 
    t.*,
    c.customer_name,
    c.customer_phone,
    pb.name AS pop_name,
    ta.name AS assigned_name,
    tt.topic_name AS issue_name
    
FROM ticket t
LEFT JOIN customers c ON t.customer_id = c.id
LEFT JOIN pop_branch pb ON t.pop_id = pb.id
LEFT JOIN ticket_assign ta ON t.asignto = ta.id
LEFT JOIN ticket_topic tt ON t.complain_type = tt.id
where c.id = {$ticket['customer_id']}
ORDER BY t.id DESC
";

$result = $con->query($sql);
$no = 1;

while($row = $result->fetch_assoc()){
?>
<tr>
    <td><?php echo $no++; ?></td>

    <!-- Status -->
    <td>
        <?php
        echo ($row['ticket_type'] == 'Complete')
            ? '<span class="badge bg-success">Complete</span>'
            : '<span class="badge bg-warning">Active</span>';
        ?>
    </td>

    <!-- Created -->
    <td><?php echo time_ago($row['create_date']); ?></td>

    <!-- Priority -->
    <td>
        <span class="badge bg-info">
            <?php echo ticket_priority($row['priority']); ?>
        </span>
    </td>

    <!-- Customer -->
    <td>
        <a href="customer_profile.php?clid=<?php echo $row['customer_id']; ?>">
            <?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?>
        </a>
    </td>

   

    <!-- Phone -->
    <td><?php echo htmlspecialchars($row['customer_phone'] ?? 'N/A'); ?></td>

    <!-- Issues -->
    <td><?php echo htmlspecialchars($row['issue_name'] ?? 'N/A'); ?></td>

    <!-- POP -->
    <td><?php echo htmlspecialchars($row['pop_name'] ?? 'N/A'); ?></td>

    <!-- Assigned -->
    <td><?php echo htmlspecialchars($row['assigned_name'] ?? 'N/A'); ?></td>

    <!-- Actual Work -->
    <td>
        <?php
            if(!empty($row['create_date']) && !empty($row['enddate'])) {
                echo acctual_work($row['create_date'], $row['enddate']);
            } else {
                echo 'N/A';
            }
        ?>
    </td>

    <!-- Completed -->
    <td>
        <?php
        echo $row['enddate']
            ? date('d M Y', strtotime($row['enddate']))
            : '<span class="text-muted">Pending</span>';
        ?>
    </td>

    <!-- Percentage -->
    <td>
        <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-success"
                style="width: <?php echo (int)$row['parcent']; ?>;">
            </div>
        </div>
        <small><?php echo $row['parcent']; ?></small>
    </td>

    <!-- Note -->
    <td><?php echo htmlspecialchars($row['customer_note']); ?></td>
    <td><?php echo htmlspecialchars($row['noc_note']); ?></td>

    <!-- Action -->
    <td class="text-end">
        <a href="ticket_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
            <i class="fas fa-edit"></i>
        </a>
        <a href="ticket_view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">
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
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
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
        $("#tickets_table").DataTable({
            "order": [[ 0, "desc" ]]
        });
           $('#commentForm').submit(function(e) {
            e.preventDefault();
            /*Get the submit button*/
            var submitBtn = $('#commentForm').find('button[type="submit"]');

            /* Save the original button text*/
            var originalBtnText = submitBtn.html();

            /*Change button text to loading state*/
            submitBtn.html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>`
            );
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            $.ajax({
                type: 'POST',
                'url': url,
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        toastr.error(response.message);
                    }
                },


                error: function(xhr, status, error) {
                    /** Handle  errors **/
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    }
                },
                complete: function() {
                    submitBtn.html(originalBtnText);
                }
            });
        });
    </script>
</body>

</html>

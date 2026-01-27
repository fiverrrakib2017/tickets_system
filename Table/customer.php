 <table id="customers_table" class="table table-bordered dt-responsive nowrap"
     style="border-collapse: collapse; border-spacing: 0; width: 100%;">
     <thead>
         <tr>
             <th>ID</th>
             <th>Name</th>
            <?php if (isset($service_customer_type) && $service_customer_type == 2): ?>
                <th>Mac Reseller</th>
            <?php elseif (isset($service_customer_type) && $service_customer_type == 1): ?>
                <th>Bandwidth</th>
            <?php else: ?>
                <th>Bandwidth / Reseller</th>
            <?php endif; ?>

            
             <th>Email</th>
             <th>Phone</th>
             <th>POP/Area</th>
             <th>Type</th>
             <th>Public IP</th>
             <th>Private IP</th>

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

            /*-------------------Mac Reseller Customer------------------*/
            if (isset($service_customer_type) && $service_customer_type == 2) {
                $reseller_query = $con->query(
                    "SELECT id 
                    FROM customers 
                    WHERE service_customer_type = 2"
                );
                while ($row = $reseller_query->fetch_assoc()) {
                    $customer_ids[] = (int)$row['id'];
                }
            }
            /*-------------------Bandwidth Customer------------------*/
            if (isset($service_customer_type) && $service_customer_type == 1) {
                $reseller_query = $con->query(
                    "SELECT id 
                    FROM customers 
                    WHERE service_customer_type = 1"
                );
                while ($row = $reseller_query->fetch_assoc()) {
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
                     <a href="customer_profile.php?clid=<?= (int) $rows['id'] ?>" class="text-decoration-none">
                         <?= htmlspecialchars($rows['customer_name']) ?>
                     </a>
                 </div>

                 <!-- Offline Duration -->
                 <?php if ($rows['ping_ip_status'] === 'offline' && !empty($rows['offline_duration'])): ?>
                 <div class="text-danger small mb-1">
                     <i class="fas fa-clock me-1"></i>
                     Offline for <?= _formate_duration((int) $rows['offline_duration']) ?>
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
                     <div class=" flex-wrap gap-3">

                         <span>
                             <i class="fas fa-paper-plane text-primary me-1"></i>
                             Sent: <strong><?= (int) $rows['ping_sent'] ?></strong>
                         </span><br>

                         <span>
                             <i class="fas fa-check-circle text-success me-1"></i>
                             Recv: <strong><?= (int) $rows['ping_received'] ?></strong>
                         </span><br>

                         <span>
                             <i class="fas fa-times-circle text-danger me-1"></i>
                             Lost: <strong><?= (int) $rows['ping_lost'] ?></strong>
                         </span><br>

                         <span>
                             <i class="fas fa-arrow-down text-info me-1"></i>
                             Min: <strong><?= (int) $rows['ping_min_ms'] ?> ms</strong>
                         </span><br>

                         <span>
                             <i class="fas fa-arrow-up text-warning me-1"></i>
                             Max: <strong><?= (int) $rows['ping_max_ms'] ?> ms</strong>
                         </span><br>

                         <span>
                             <i class="fas fa-chart-line text-secondary me-1"></i>
                             Avg: <strong><?= (int) $rows['ping_avg_ms'] ?> ms</strong>
                         </span><br>

                     </div>

                 </div>

             </td>
             <td>
                 <?php
                 if ($rows['service_customer_type'] == 1) {
                     /* Bandwidth Service */
                     if (function_exists('get_customer_services')) {
                         print_r(get_customer_services($rows['id']));
                     }
                 } elseif ($rows['customer_type_id'] == 2) {
                     /* Mac Reseller Service */
                     if (function_exists('get_customer_mac_reseller_services')) {
                         print_r(get_customer_mac_reseller_services($rows['id']));
                     }
                 } else {
                     echo 'N/A';
                 }
                 
                 ?>
             </td>

             <td><?php echo htmlspecialchars($rows['customer_email'] ?? 'N/A'); ?></td>
             <td>
                 <?php echo $rows['phones'] ? $rows['phones'] : 'N/A'; ?>
             </td>


             <td><?php echo htmlspecialchars($rows['pop_branch_name']); ?></td>
             <td><?php echo htmlspecialchars($rows['type_name']); ?></td>
             <td><?php echo $rows['public_ip_address'] ?? 'N/A'; ?></td>
             <td><?php echo $rows['private_customer_ip'] ?? 'N/A'; ?></td>

             <td><?php echo (int) $rows['total'] ?? '0'; ?> MBPS</td>
             <td>
                 <?php echo $rows['status'] == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
             </td>
             <td style="text-align:right">
                 <a href="customer_profile_edit.php?clid=<?php echo $rows['id']; ?>" class="btn-sm btn btn-info">
                     <i class="fas fa-edit"></i>
                 </a>
                 <button type="button" name="delete_button" data-id="<?php echo $rows['id']; ?>"
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

<script src="assets/libs/jquery/jquery.min.js"></script>
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
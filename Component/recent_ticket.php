<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Recent Tickets</h4>

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
ORDER BY t.id DESC
LIMIT 10

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
      

            if($row['ticket_type']=='Active'){
                echo '<span class="badge bg-danger">Active</span>';
            }else if($row['ticket_type']=='Complete'){
                echo '<span class="badge bg-success">Complete</span>';
            }else if($row['ticket_type']=='Open'){
                echo '<span class="badge bg-warning">Open</span>';
            }else if($row['ticket_type']=='Open'){
                echo '<span class="badge bg-warning">Open</span>';
            }else if($row['ticket_type']=='New'){
                echo '<span class="badge bg-warning">New</span>';
            }else if($row['ticket_type']=='Close'){
                echo '<span class="badge bg-danger">Close</span>';
            }else{
                echo '-----';
            }
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
    </div>
</div>

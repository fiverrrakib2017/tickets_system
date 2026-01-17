<?php
$no = 1;
while ($row = $tickets->fetch_assoc()):
?>
<tr>
    <td><?= $no++ ?></td>

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

    <td><?= time_ago($row['create_date']); ?></td>

    <td>
        <span class="badge bg-info">
            <?= ticket_priority($row['priority']); ?>
        </span>
    </td>

    <td>
        <a href="customer_profile.php?clid=<?= $row['customer_id']; ?>">
            <?= htmlspecialchars($row['customer_name'] ?? 'N/A'); ?>
        </a>
    </td>

    <td><?= $row['phones'] ?: 'N/A'; ?></td>

    <td><?= htmlspecialchars($row['issue_name'] ?? 'N/A'); ?></td>

    <td><?= htmlspecialchars($row['pop_name'] ?? 'N/A'); ?></td>

    <td><?= htmlspecialchars($row['assigned_name'] ?? 'N/A'); ?></td>

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
<?php endwhile; ?>

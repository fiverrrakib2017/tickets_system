
 
 <div class="col-md-6 mb-3 d-none">
     <label class="form-label">Ticket id</label>
     <input type="text" name="ticket_id" class="form-control"
         value="<?= isset($ticket['id']) ? htmlspecialchars($ticket['id']) : '' ?>" >
 </div>




<div class="col-md-6 mb-3">
    <label class="form-label">Assign To</label>

    <select name="assign_user_id" class="form-select" required>
        <option value="">---select---</option>

        <?php
        $session_uid = $_SESSION['uid'] ?? 0;

        $ticket_assign_users = $con->query("SELECT * FROM users WHERE department_id !='4' ");

        while ($row = $ticket_assign_users->fetch_assoc()) {

            if (!empty($ticket['assign_user_id'])) {
                $selected = ($row['id'] == $ticket['assign_user_id']) ? 'selected' : '';
            } else {
                $selected = ($row['id'] == $session_uid) ? 'selected' : '';
            }

            echo "<option value='{$row['id']}' {$selected}>{$row['fullname']}</option>";
        }
        ?>
    </select>
</div>




<div class="col-md-6 mb-3 ">
    <label class="form-label">Ticket For</label>
    <select id="ticket_for" name="ticket_for" class="form-select" required>
        <option value="">--- Select ---</option>

        <option value="Mac Client"
            <?= (isset($ticket['ticketfor']) && $ticket['ticketfor'] === 'Mac Client') ? 'selected' : ''; ?> <?= (isset($service_customer_type) && $service_customer_type == '2') ? 'selected' : '' ?>>
            Mac Client
        </option>

        <option value="Bandwidth Client"
            <?= (isset($ticket['ticketfor']) && $ticket['ticketfor'] === 'Bandwidth Client') ? 'selected' : ''; ?>  <?= (isset($service_customer_type) && $service_customer_type == '1') ? 'selected' : '' ?>
            >
            Bandwidth Client
        </option>

        <option value="Corporate Client"
            <?= (isset($ticket['ticketfor']) && $ticket['ticketfor'] === 'Corporate Client') ? 'selected' : ''; ?>>
            Corporate Client
        </option>
    </select>
</div>
<div class="col-md-6 mb-3 ">
    <label class="form-label">Ticket Priority</label>
    <select id="ticket_priority" name="ticket_priority" class="form-select" required>
        <option value="">--- Select ---</option>

        <option value="1" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 1) ? 'selected' : ''; ?>>
            Low
        </option>

        <option value="2" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 2) ? 'selected' : ''; ?>>
            Normal
        </option>

        <option value="3" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 3) ? 'selected' : ''; ?> >
            Standard
        </option>

        <option value="4" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 4) ? 'selected' : ''; ?>>
            Medium
        </option>

        <option value="5" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 5) ? 'selected' : ''; ?>>
            High
        </option>

        <option value="6" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 6) ? 'selected' : ''; ?>>
            Very High
        </option>
    </select>
</div>
<div class="col-md-6 mb-3">
    <label class="form-label">Complain Type</label>
    <select name="complain_type" class="form-select">
       <option value="">---select---</option>
        <?php
        $complain_type = $con->query('SELECT * FROM ticket_topic');
        while ($row = $complain_type->fetch_assoc()) {
            $selected = $row['id'] == $ticket['complain_type'] ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected>{$row['topic_name']}</option>";
        }
        ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label  class="form-label">Customer Note</label>
    <input  type="text" name="customer_note" class="form-control" value="<?= isset($ticket['customer_note']) ? $ticket['customer_note'] : '' ?>" placeholder="Enter Your Note">
</div>
<div class="col-md-6 mb-3">
    <label  class="form-label">NOC Note</label>
    <select  type="text" name="noc_note_select" class="form-control">
        <option>---Select---</option>
        <?php 
        if($get_all_noc_note=$con->query("SELECT * FROM noc_note")){
            while($rows=$get_all_noc_note->fetch_array()){
                echo '<option value="'.$rows['name'].'">'.$rows['name'].'</option>';
            }
        }
        
        ?>
    </select>
</div>
<div class="col-md-6 mb-3">
    <label  class="form-label">NOC Note</label>
    <input  type="text" name="noc_note" class="form-control" value="<?= isset($ticket['noc_note']) ? $ticket['noc_note'] : '' ?>" placeholder="Enter Your NOC Note">
</div>
<div class="col-md-6 mb-3">
    <label  class="form-label">Subject</label>
    <input  type="text" name="customer_subject" class="form-control" value="<?= isset($ticket['subject']) ? $ticket['subject'] : '' ?>" placeholder="Enter Your Subject">
</div>

<div class="col-md-6 mb-3">
    <label  class="form-label">Description</label>
    <textarea  type="text" name="customer_description" class="form-control" placeholder="Enter Description"><?= isset($ticket['description']) ? $ticket['description'] : '' ?></textarea>
</div>

<div class="col-md-6 mb-3">
    <label  class="form-label">Attachments</label>
    <input type="file" name="customer_attachments" class="form-control" value="<?= isset($ticket['attachments']) ? $ticket['attachments'] : '' ?>">
</div>


<script src="http://103.112.206.139/assets/libs/jquery/jquery.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        
        $(document).on('change','select[name="noc_note_select"]',function(){
            const noc_note_value = $(this).val();    
            $('input[name="noc_note"]').val(noc_note_value);
        });
    });
    
</script>
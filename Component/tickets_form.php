 
 <?php 
 /*------------If using customer portal----------------*/
 $_is_customer_portal=false; 
 if(isset($_SESSION['customer']['id'])){
    $_is_customer_portal=true; 
    $customer_id= (int)$_SESSION['customer']['id'];
    $service_customer_type= $con->query("SELECT `customer_type_id` FROM customers WHERE id=$customer_id")->fetch_assoc()['customer_type_id'];
    $customer_pop_id= $con->query("SELECT `pop_id` FROM customers WHERE id=$customer_id")->fetch_assoc()['pop_id'];
 }
 
 
 
 ?>
 
 
 <div class="col-md-6 mb-3 d-none">
     <label class="form-label">Ticket id</label>
     <input type="text" name="ticket_id" class="form-control"
         value="<?= isset($ticket['id']) ? htmlspecialchars($ticket['id']) : '' ?>" >
 </div>

<div class="col-md-6 mb-3  <?= $_is_customer_portal ? 'd-none' : '' ?>">
    <label class="form-label">Customer Name</label>
    <select name="customer_id" class="form-select" required>
        <option value="">---select---</option>
        <?php
        $customers = $con->query('SELECT * FROM customers');
        while ($row = $customers->fetch_assoc()) {
            $selected = $row['id'] == $ticket['customer_id'] ? 'selected' : '';
            $selected_for_customer_portal= $row['id'] == $customer_id ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected  $selected_for_customer_portal>{$row['customer_name']}</option>";
        }
        ?>
    </select>
</div>
<div class="col-md-6 mb-3 d-none" id="show_customer_ip_div">
    <label class="form-label">Customer IP</label>
    <input name="text" id="show_customer_ip" class="form-control" value="">
</div>



<?php if($_is_customer_portal==false):?>
<div class="col-md-6 mb-3">
    <label class="form-label">Assign To</label>
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
<?php endif; ?>


<?php if($_is_customer_portal==false):?>
<div class="col-md-6 mb-3 ">
    <label class="form-label">POP / Area</label>
    <select name="pop_branch" class="form-select" required>
        <option value="">---select---</option>
        <?php
        $pop = $con->query('SELECT * FROM pop_branch');
        while ($row = $pop->fetch_assoc()) {
            $selected = $row['id'] == $ticket['pop_id'] ? 'selected' : '';
            $selected_pop_for_customer_portal= $row['id'] == $customer_pop_id ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected $selected_pop_for_customer_portal>{$row['name']}</option>";
        }
        ?>
    </select>
</div>
<?php endif; ?>

<div class="col-md-6 mb-3 d-none" id="show_pop_branch_ip_div">
    <label class="form-label">Router/Switch IP</label>
    <input name="text" id="show_pop_branch_ip" class="form-control" value="">
</div>

<div class="col-md-6 mb-3 <?= $_is_customer_portal ? 'd-none' : '' ?>">
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
<div class="col-md-6 mb-3 <?= $_is_customer_portal ? 'd-none' : '' ?>">
    <label class="form-label">Ticket Priority</label>
    <select id="ticket_priority" name="ticket_priority" class="form-select" required>
        <option value="">--- Select ---</option>

        <option value="1" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 1) ? 'selected' : ''; ?>>
            Low
        </option>

        <option value="2" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 2) ? 'selected' : ''; ?>>
            Normal
        </option>

        <option value="3" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 3) ? 'selected' : ''; ?> <?= $_is_customer_portal ? 'selected' : '' ?>>
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
<?php if($_is_customer_portal==false):?>
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
<?php endif; ?>

<?php if($_is_customer_portal==false):?>
<div class="col-md-6 mb-3">
    <label  class="form-label">Customer Note</label>
    <input  type="text" name="customer_note" class="form-control" value="<?= isset($ticket['customer_note']) ? $ticket['customer_note'] : '' ?>" placeholder="Enter Your Note">
</div>
<?php endif; ?>
<?php if($_is_customer_portal==false):?>
<div class="col-md-6 mb-3">
    <label  class="form-label">NOC Note</label>
    <input  type="text" name="noc_note" class="form-control" value="<?= isset($ticket['noc_note']) ? $ticket['noc_note'] : '' ?>" placeholder="Enter Your NOC Note">
</div>
<?php endif; ?>
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
<?php if($_is_customer_portal==false):?>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('change', 'select[name="customer_id"]', function () {
            let customer_id = $(this).val();
            if (customer_id === '') {
                $('#show_customer_ip').val('');
                return;
            }

            $.ajax({
                url: 'http://103.112.206.139/include/customer_server.php?get_customer_ping_ip=true',
                type: 'GET',
                data: { customer_id: customer_id },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        if(response.ping_ip.length > 0){
                            $("#show_customer_ip_div").removeClass('d-none');
                             $('#show_customer_ip').val(response.ping_ip);
                        } 
                       
                    } else {
                        $('#show_customer_ip').val('');
                    }
                }
            });
        }); 
       $(document).on('change', 'select[name="pop_branch"]', function () {
            let pop_id = $(this).val();
            if (pop_id === '') {
                $('#show_pop_branch_ip').val('');
                return;
            }

            $.ajax({
                url: 'http://103.112.206.139/include/pop_branch_server.php?get_pop_branch_ping_ip=true',
                type: 'GET',
                data: { pop_id: pop_id },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $("#show_pop_branch_ip_div").removeClass('d-none');
                        $('#show_pop_branch_ip').val(response.router_ip);
                    } else {
                        $('#show_pop_branch_ip').val('');
                    }
                }
            });
        }); 
    });
    
</script>
<?php endif;?>
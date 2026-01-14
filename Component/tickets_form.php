<div class="col-md-6 mb-3">
    <label class="form-label">Customer Name</label>
    <select name="customer_id" class="form-select" required>
        <option value="">---select---</option>
        <?php
        $customers = $con->query('SELECT * FROM customers');
        while ($row = $customers->fetch_assoc()) {
            $selected = $row['id'] == $ticket['customer_id'] ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected>{$row['customer_name']}</option>";
        }
        ?>
    </select>
</div>
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


<div class="col-md-6 mb-3">
    <label class="form-label">POP / Area</label>
    <select name="pop_branch" class="form-select" required>
        <option value="">---select---</option>
        <?php
        $pop = $con->query('SELECT * FROM pop_branch');
        while ($row = $pop->fetch_assoc()) {
            $selected = $row['id'] == $ticket['pop_id'] ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
        }
        ?>
    </select>
</div>
<div class="col-md-6 mb-3">
        <label  class="form-label">Ticket For</label>
        <select id="ticket_for" name="ticket_for" class="form-select" required>
            <option value="Home Connection">Home Connection</option>
            <option value="POP">POP Support</option>
            <option value="Corporate">Corporate</option>
        </select>
</div>
<div class="col-md-6 mb-3">
        <label  class="form-label">Ticket Priority</label>
        <select id="ticket_priority" name="ticket_priority" class="form-select" style="width: 100%;">
           <option value="">---select---</option>
            <option value="1">Low</option>
            <option value="2">Normal</option>
            <option value="3">Standard</option>
            <option value="4">Medium</option>
            <option value="5">High</option>
            <option value="6">Very High</option>
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
    <label  class="form-label">Note</label>
    <input id="notes" type="text" name="notes" class="form-control" placeholder="Enter Your Note">
</div>



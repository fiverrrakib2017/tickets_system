 <div class="col-md-6 mb-3 d-none">
     <label class="form-label">Ticket id</label>
     <input type="text" name="ticket_id" class="form-control"
         value="<?= isset($ticket['id']) ? htmlspecialchars($ticket['id']) : '' ?>" >
 </div>
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
    <label class="form-label">Ticket For</label>
    <select id="ticket_for" name="ticket_for" class="form-select" required>
        <option value="">--- Select ---</option>

        <option value="Mac Client"
            <?= (isset($ticket['ticketfor']) && $ticket['ticketfor'] === 'Mac Client') ? 'selected' : ''; ?>>
            Mac Client
        </option>

        <option value="Bandwidth Client"
            <?= (isset($ticket['ticketfor']) && $ticket['ticketfor'] === 'Bandwidth Client') ? 'selected' : ''; ?>>
            Bandwidth Client
        </option>

        <option value="Corporate Client"
            <?= (isset($ticket['ticketfor']) && $ticket['ticketfor'] === 'Corporate Client') ? 'selected' : ''; ?>>
            Corporate Client
        </option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Ticket Priority</label>
    <select id="ticket_priority" name="ticket_priority" class="form-select" required>
        <option value="">--- Select ---</option>

        <option value="1" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 1) ? 'selected' : ''; ?>>
            Low
        </option>

        <option value="2" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 2) ? 'selected' : ''; ?>>
            Normal
        </option>

        <option value="3" <?= (isset($ticket['priority']) && (int)$ticket['priority'] === 3) ? 'selected' : ''; ?>>
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
    <input id="notes" type="text" name="customer_note" class="form-control" value="<?= isset($ticket['customer_note']) ? $ticket['customer_note'] : '' ?>" placeholder="Enter Your Note">
</div>
<div class="col-md-6 mb-3">
    <label  class="form-label">NOC Note</label>
    <input id="notes" type="text" name="noc_note" class="form-control" value="<?= isset($ticket['noc_note']) ? $ticket['noc_note'] : '' ?>" placeholder="Enter Your NOC Note">
</div>



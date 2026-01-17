 <div class="col-md-6 mb-3 d-none">
     <label class="form-label">Customer id</label>
     <input type="text" name="customer_id" class="form-control"
         value="<?= isset($customer['id']) ? htmlspecialchars($customer['id']) : '' ?>" >
 </div>
 <div class="col-md-6 mb-3">
     <label class="form-label">Customer Name</label>
     <input type="text" name="customer_name" class="form-control" placeholder="Enter Customer Name"
         value="<?= isset($customer['customer_name']) ? htmlspecialchars($customer['customer_name']) : '' ?>" required>
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Email</label>
     <input type="email" name="customer_email" class="form-control" placeholder="Enter Customer Email"
         value="<?= isset($customer['customer_email']) ? htmlspecialchars($customer['customer_email']) : '' ?>">
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Phone Number</label>
     <input type="text" name="customer_phone" class="form-control" placeholder="Enter Phone Number"
         value="<?= isset($customer['customer_phone']) ? htmlspecialchars($customer['customer_phone']) : '' ?>">
 </div>
 <div class="col-md-6 mb-3">
     <label class="form-label">Phone Number 2</label>
     <input type="text" name="customer_phone_2" class="form-control" placeholder="Enter Phone Number"
         value="<?= isset($customer['phone_number']) ? htmlspecialchars($customer['phone_number']) : '' ?>">
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">POP / Area</label>
     <select name="customer_pop_branch" class="form-select" required>
         <?php
         $pop = $con->query('SELECT * FROM pop_branch');
         while ($row = $pop->fetch_assoc()) {
             $selected = $row['id'] == $customer['pop_id'] ? 'selected' : '';
             echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
         }
         ?>
     </select>
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Connection Via</label>
     <select name="customer_type" class="form-select">
         <?php
         $types = $con->query('SELECT * FROM customer_type');
         while ($row = $types->fetch_assoc()) {
             $selected = $row['id'] == $customer['customer_type_id'] ? 'selected' : '';
             echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
         }
         ?>
     </select>
 </div>



 <div class="col-md-6 mb-3">
     <label class="form-label">VLAN</label>
     <input type="text" name="customer_vlan" class="form-control" placeholder="Enter VLAN"
         value="<?= isset($customer['customer_vlan']) ? htmlspecialchars($customer['customer_vlan']) : '' ?>">
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Public Address</label>
     <input type="text" name="customer_ip" class="form-control" placeholder="Enter IP Address"
         value="<?= isset($customer['customer_ip']) ? htmlspecialchars($customer['customer_ip']) : '' ?>">
 </div>
 <div class="col-md-6 mb-3">
     <label class="form-label">Private Address</label>
     <input type="text" name="private_customer_ip" class="form-control" placeholder="Enter IP Address"
         value="<?= isset($customer['private_customer_ip']) ? htmlspecialchars($customer['private_customer_ip']) : '' ?>">
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Status</label>
     <select name="customer_status" class="form-select">
         <option value="1" <?php if (isset($customer['status']) && $customer['status'] == 1) {
             echo 'selected';
         } ?>>Active</option>
         <option value="0" <?php if (isset($customer['status']) && $customer['status'] == 0) {
             echo 'selected';
         } ?>>Inactive</option>
     </select>
 </div>

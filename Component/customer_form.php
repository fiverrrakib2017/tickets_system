 <div class="col-md-6 mb-3">
                                                <label class="form-label">Customer Name</label>
                                                <input type="text" name="customer_name" class="form-control"
                                                    placeholder="Enter Customer Name"  value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="customer_email" class="form-control"
                                                    placeholder="Enter Customer Email"  value="<?php echo htmlspecialchars($customer['customer_email']); ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" name="customer_phone" class="form-control"
                                                    placeholder="Enter Phone Number" value="<?php echo htmlspecialchars($customer['customer_phone']); ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">POP / Area</label>
                                                 <select name="customer_pop_branch" class="form-select" required>
                                                    <?php
                                                        $pop = $con->query("SELECT * FROM pop_branch");
                                                        while($row = $pop->fetch_assoc()){
                                                            $selected = ($row['id'] == $customer['pop_id']) ? 'selected' : '';
                                                            echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Connection Via</label>
                                                 <select name="customer_type" class="form-select">
                                                    <?php
                                                        $types = $con->query("SELECT * FROM customer_type");
                                                        while($row = $types->fetch_assoc()){
                                                            $selected = ($row['id'] == $customer['customer_type_id']) ? 'selected' : '';
                                                            echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">VLAN</label>
                                                <input type="text" name="customer_vlan" class="form-control"
                                                    placeholder="Enter VLAN"  value="<?php echo htmlspecialchars($customer['customer_vlan']); ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">IP Address</label>
                                                <input type="text" name="customer_ip" class="form-control"
                                                    placeholder="Enter IP Address" value="<?php echo htmlspecialchars($customer['customer_ip']); ?>">
                                            </div>

                                             <div class="col-md-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="customer_status" class="form-select">
                                                    <option value="1" <?php if($customer['status']==1) echo 'selected'; ?>>Active</option>
                                                    <option value="0" <?php if($customer['status']==0) echo 'selected'; ?>>Inactive</option>
                                                </select>
                                            </div>
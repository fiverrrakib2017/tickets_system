 <!--Add Modal -->
    <div class="modal fade " tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="addCustomerModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span
                            class="mdi mdi-account-check mdi-18px"></span> &nbsp;Create Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="include/customer_server.php?add_customer_data=true" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label>Customer Name</label>
                                        <input type="text" name="customer_name" class="form-control" placeholder="Enter Customer Name">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Email</label>
                                        <input type="text" name="customer_email" class="form-control" placeholder="Enter Customer Email">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Phone Number</label>
                                        <input type="text" name="customer_phone" class="form-control" placeholder="Enter Customer Phone Number">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Area/Location</label>
                                        <input type="text" name="customer_location" class="form-control" placeholder="Enter Customer Area/Location">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Type</label>
                                        <select type="text" name="customer_type" class="form-select" placeholder="Enter Customer Type">
                                            <option value="">Select Type</option>
                                           
                                            <?php 
                                            if($con->query("SELECT * FROM customer_type")){
                                                $types = $con->query("SELECT * FROM customer_type");
                                                while($row = $types->fetch_array()){
                                                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }
                                            }
                                            
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>V-LAN</label>
                                        <input type="text" name="customer_vlan" class="form-control" placeholder="Enter Customer VLAN">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>IP Address</label>
                                        <input type="text" name="customer_ip" class="form-control" placeholder="Enter Customer IP Address">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Bandwidth</label>
                                        <input type="text" name="customer_bandwidth" class="form-control" placeholder="Enter Customer Bandwidth">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Status</label>
                                        <select type="text" name="customer_status" class="form-select" placeholder="Enter Customer Status">
                                            <option value="">Select Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
 <!--Add Modal -->
    <div class="modal fade " tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editCustomerModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span
                            class="mdi mdi-account-check mdi-18px"></span> &nbsp;Update Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="include/customer_server.php?update_customer_data=true" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label>Customer Name</label>
                                        <input type="text" name="id" class="d-none" >
                                        <input type="text" name="customer_name" class="form-control" placeholder="Enter Customer Name">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Email</label>
                                        <input type="text" name="customer_email" class="form-control" placeholder="Enter Customer Email">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Phone Number</label>
                                        <input type="text" name="customer_phone" class="form-control" placeholder="Enter Customer Phone Number">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Area/Location</label>
                                        <input type="text" name="customer_location" class="form-control" placeholder="Enter Customer Area/Location">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Type</label>
                                        <select type="text" name="customer_type" class="form-select" placeholder="Enter Customer Type">
                                            <option value="">Select Type</option>
                                           
                                            <?php 
                                            if($con->query("SELECT * FROM customer_type")){
                                                $types = $con->query("SELECT * FROM customer_type");
                                                while($row = $types->fetch_array()){
                                                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }
                                            }
                                            
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>V-LAN</label>
                                        <input type="text" name="customer_vlan" class="form-control" placeholder="Enter Customer VLAN">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>IP Address</label>
                                        <input type="text" name="customer_ip" class="form-control" placeholder="Enter Customer IP Address">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Bandwidth</label>
                                        <input type="text" name="customer_bandwidth" class="form-control" placeholder="Enter Customer Bandwidth">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Status</label>
                                        <select type="text" name="customer_status" class="form-select" placeholder="Enter Customer Status">
                                            <option value="">Select Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
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
                                        <label>POP/Area</label>
                                        <select type="text" name="customer_pop_branch" class="form-select">
                                            <option value="">---Select---</option>
                                            <?php 
                                            if($con->query("SELECT * FROM pop_branch")){
                                                $pop_branches = $con->query("SELECT * FROM pop_branch");
                                                while($row = $pop_branches->fetch_array()){
                                                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Connection Via</label>
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
                                        <input type="text" name="customer_bandwidth" class="form-control" placeholder="Enter Customer Bandwidth"  value="IIG 200Mbps | GGC 500Mbps FNA 500Mbps | CDN 500Mbps">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>IIG</label>
                                        <input type="number" name="customer_bandwidth" class="form-control" placeholder="Enter Customer Bandwidth"  value="200">
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
                                        <label>POP/Area</label>
                                        <select type="text" name="customer_pop_branch" class="form-select">
                                            <option value="">---Select---</option>
                                            <?php 
                                            if($con->query("SELECT * FROM pop_branch")){
                                                $pop_branches = $con->query("SELECT * FROM pop_branch");
                                                while($row = $pop_branches->fetch_array()){
                                                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Connection Via</label>
                                        <select type="text" name="customer_type" class="form-select" placeholder="Enter Customer Type">
                                            <option value="">---Select---</option>
                                           
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
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 


     <script type="text/javascript">
        
        $('#addCustomerModal form').submit(function(e) {
                e.preventDefault();
                /*Get the submit button*/
                var submitBtn = $('#addCustomerModal form').find('button[type="submit"]');

                /* Save the original button text*/
                var originalBtnText = submitBtn.html();

                /*Change button text to loading state*/
                submitBtn.html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>`
                );
                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();
                $.ajax({
                    type: 'POST',
                    'url': url,
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#addCustomerModal').modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            toastr.error(response.message);
                        }
                    },


                    error: function(xhr, status, error) {
                        /** Handle  errors **/
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                    }
                });
            });
             /** Edit Area Script **/
            $(document).on('click', "button[name='edit_button']", function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "include/customer_server.php?get_customer_data=true",
                    type: "GET",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editCustomerModal').modal('show');
                            $('#editCustomerModal input[name="id"]').val(response.data.id);
                            $('#editCustomerModal input[name="customer_name"]').val(response.data.customer_name);
                            $('#editCustomerModal input[name="customer_email"]').val(response.data.customer_email);
                            $('#editCustomerModal input[name="customer_phone"]').val(response.data.customer_phone);
                            $('#editCustomerModal select[name="customer_pop_branch"]').val(response.data.pop_id);
                            $('#editCustomerModal select[name="customer_type"]').val(response.data.customer_type_id);
                            $('#editCustomerModal input[name="customer_ip"]').val(response.data.customer_ip);
                            $('#editCustomerModal input[name="customer_bandwidth"]').val(response.data.customer_bandwidth);
                            $('#editCustomerModal select[name="customer_status"]').val(response.data.status);

                        } else {
                            toastr.error("Error fetching data for edit: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to fetch  details');
                    }
                });
            });
            /** Update **/
            $('#editCustomerModal form').submit(function(e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();

                /*Get the submit button*/
                var submitBtn = form.find('button[type="submit"]');

                /*Save the original button text*/
                var originalBtnText = submitBtn.html();

                /*Change button text to loading state*/


                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();
                /** Use Ajax to send the delete request **/
                $.ajax({
                    type: 'POST',
                    'url': url,
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        form.find(':input').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                             $('#editCustomerModal').modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 500);

                        }
                        if (response.success == false) {
                            toastr.error(response.message);
                        }
                    },

                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    },
                    complete: function() {
                        form.find(':input').prop('disabled', false);
                    }
                });
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
    </script>